<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Guest;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\SpiceCategory;
use App\Models\SpiceOrder;
use App\Models\SpiceOrderItem;
use App\Models\SpiceProduct;
use App\Models\SpiceReturnRule;
use App\Services\RazorpayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientSpiceController extends Controller
{
    /**
     * Public Spices Shop Catalogue.
     * Accessible by unauthenticated and authenticated guests alike.
     * Enforces STRICT ACTIVE-ONLY VISIBILITY.
     */
    public function index(Request $request)
    {
        $categories = SpiceCategory::where('is_active', true)->orderBy('sort_order')->get();
        $categoryId = $request->query('category_id');
        $search = $request->query('q');

        // Strictly active products only (Pack-on-order harvest, no numeric stock gating)
        $query = SpiceProduct::with('category')->active();

        if ($categoryId) {
            $query->where('spice_category_id', $categoryId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('sort_order')->get();

        // Load active return rules and policy description for transparency
        $returnRules = SpiceReturnRule::active()->orderBy('sort_order')->get();
        $policyDescription = Setting::get(
            'spice_return_policy_description',
            'All Krishna Spices are harvested and vacuum-ground on-demand after receiving your order to guarantee plantation freshness. You can cancel your order with a 100% full refund at any time before dispatch. Unopened, factory-sealed spice packs can be returned within 7 days of delivery under our 90% freshness guarantee.'
        );

        $globalReturnsEnabled = (bool) Setting::get('spice_returns_enabled', true);

        return view('client.spices.index', compact('categories', 'products', 'categoryId', 'search', 'returnRules', 'policyDescription', 'globalReturnsEnabled'));
    }

    /**
     * Checkout Screen.
     * Requires authentication; non-checked in clients can purchase with courier shipping.
     * In-house checked-in guests also have the option of Villa delivery.
     */
    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            session(['url.intended' => route('spices.checkout')]);
            return redirect()->route('login')->with('info', 'Please sign in to your account to complete your spice order.');
        }

        $user = Auth::user();
        $guest = $user->guest ?? Guest::where('email', $user->email)->orWhere('phone', $user->phone)->first();

        // Check if user has an active checked-in stay
        $today = Carbon::today()->format('Y-m-d');
        $activeStay = Reservation::with(['branch', 'room'])
            ->where(function ($q) use ($user, $guest) {
                $q->where('created_by', $user->id);
                if ($guest) $q->orWhere('guest_id', $guest->id);
            })
            ->where('status', 'checked_in')
            ->where('check_out_date', '>=', $today)
            ->first();

        $globalReturnsEnabled = (bool) Setting::get('spice_returns_enabled', true);

        return view('client.spices.checkout', compact('user', 'guest', 'activeStay', 'globalReturnsEnabled'));
    }

    /**
     * Create Razorpay Order for Spice Store checkout
     */
    public function createRazorpayOrder(Request $request, RazorpayService $razorpayService): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in to place your spice order.'], 401);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:30',
            'delivery_mode' => 'nullable|string|in:courier,villa',
            'items_json' => 'required|string',
        ]);

        $cartItems = json_decode($validated['items_json'], true);
        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Your shopping cart is empty.'], 422);
        }

        $deliveryMode = $validated['delivery_mode'] ?? 'courier';
        $isVilla = ($deliveryMode === 'villa');

        $subtotal = 0;
        $totalTax = 0;

        foreach ($cartItems as $itemInput) {
            $product = SpiceProduct::active()->find($itemInput['id']);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'One or more items in your cart is paused or unavailable.'], 422);
            }

            $type = ($itemInput['type'] ?? 'packet') === 'loose' ? 'loose' : 'packet';
            $taxRate = (float)($product->tax_rate ?: 5.00);

            if ($type === 'loose') {
                $weightKg = max(0.05, (float)($itemInput['weight_kg'] ?? 1.0));
                $unitPrice = (float)($product->price_per_kg ?: round($product->price * (1000 / ($product->weight_grams ?: 100)), 2));
                $lineTotal = round($unitPrice * $weightKg, 2);
                $lineTax = round($lineTotal * ($taxRate / 100), 2);
            } else {
                $qty = max(1, (int)($itemInput['quantity'] ?? 1));
                $unitPrice = (float)$product->price;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineTax = round($lineTotal * ($taxRate / 100), 2);
            }

            $subtotal += $lineTotal;
            $totalTax += $lineTax;
        }

        $shipping = $isVilla ? 0 : (($subtotal >= 999) ? 0 : 80);
        $total = $subtotal + $shipping + $totalTax;

        $receiptId = 'KSP-' . date('ymd') . '-' . rand(1000, 9999);

        try {
            $order = $razorpayService->createOrder($total, $receiptId, [
                'type' => 'spice_store',
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'delivery_mode' => $deliveryMode,
            ]);

            return response()->json([
                'success' => true,
                'key_id' => $razorpayService->getKeyId(),
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'amount_rupees' => $total,
                'currency' => $order['currency'],
                'name' => 'Krishna Spices',
                'description' => 'Single-Estate Spices Direct from Plantation',
                'prefill' => [
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'contact' => $validated['customer_phone'],
                ],
                'theme' => [
                    'color' => '#063F34',
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to initialize Razorpay checkout: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process Spice Order (Cart Submission).
     * Strictly verifies that all items are active.
     */
    public function placeOrder(Request $request)
    {
        if (!Auth::check()) {
            session(['url.intended' => route('spices.checkout')]);
            return redirect()->route('login')->with('error', 'Please sign in to place your spice order.');
        }

        $user = Auth::user();
        $guest = $user->guest ?? Guest::where('email', $user->email)->orWhere('phone', $user->phone)->first();

        $rules = [
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:30',
            'delivery_mode' => 'nullable|string|in:courier,villa',
            'room_number' => 'nullable|string|max:50',
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_pincode' => 'nullable|string|max:20',
            'payment_method' => 'required|string|in:upi,card,cod,charge_to_room',
            'items_json' => 'required|string',
        ];

        $isOnlinePayment = in_array($request->input('payment_method'), ['upi', 'card']);
        if ($isOnlinePayment) {
            $rules['razorpay_payment_id'] = 'required|string';
            $rules['razorpay_order_id'] = 'required|string';
            $rules['razorpay_signature'] = 'required|string';
        }

        $validated = $request->validate($rules);

        if ($isOnlinePayment) {
            $razorpayService = app(RazorpayService::class);
            $isValid = $razorpayService->verifySignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            if (!$isValid) {
                return back()->withInput()->with('error', 'Online payment verification failed (invalid signature). If money was debited, contact concierge with reference ' . $validated['razorpay_payment_id']);
            }
        }

        $cartItems = json_decode($validated['items_json'], true);
        if (empty($cartItems)) {
            return back()->with('error', 'Your shopping cart is empty.');
        }

        $deliveryMode = $validated['delivery_mode'] ?? 'courier';
        $isVilla = ($deliveryMode === 'villa');

        if (!$isVilla) {
            if (empty($validated['shipping_address_line1']) || empty($validated['shipping_city']) || empty($validated['shipping_pincode'])) {
                return back()->with('error', 'Please provide a valid Indian shipping address for courier delivery.');
            }
        }

        $subtotal = 0;
        $totalTax = 0;
        $orderItemsData = [];

        foreach ($cartItems as $itemInput) {
            // STRICT ACTIVE-ONLY CHECK
            $product = SpiceProduct::active()->find($itemInput['id']);
            if (!$product) {
                return back()->with('error', 'One or more items in your cart is currently paused or inactive for plantation harvest. Please review your cart.');
            }

            $type = ($itemInput['type'] ?? 'packet') === 'loose' ? 'loose' : 'packet';
            $taxRate = (float)($product->tax_rate ?: 5.00);

            if ($type === 'loose') {
                $weightKg = max(0.05, (float)($itemInput['weight_kg'] ?? 1.0));
                $unitPrice = (float)($product->price_per_kg ?: round($product->price * (1000 / ($product->weight_grams ?: 100)), 2));
                $lineTotal = round($unitPrice * $weightKg, 2);
                $lineTax = round($lineTotal * ($taxRate / 100), 2);
                $weightDisplay = number_format($weightKg, 2) . ' kg loose';
                $qty = 1;
            } else {
                $qty = max(1, (int)($itemInput['quantity'] ?? 1));
                $weightKg = ($product->weight_grams ? ($product->weight_grams * $qty / 1000) : null);
                $unitPrice = (float)$product->price;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineTax = round($lineTotal * ($taxRate / 100), 2);
                $weightDisplay = ($product->package_size ?: ($product->weight_grams . 'g Pack')) . ($qty > 1 ? " × {$qty}" : '');
            }

            $subtotal += $lineTotal;
            $totalTax += $lineTax;

            $orderItemsData[] = [
                'spice_product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'pricing_type' => $type,
                'weight_kg' => $weightKg,
                'weight_display' => $weightDisplay,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'total_price' => $lineTotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $lineTax,
            ];
        }

        // Shipping calculation: In-Villa is Free. Courier is free above ₹999, else ₹80.
        $shipping = $isVilla ? 0 : (($subtotal >= 999) ? 0 : 80);
        $total = $subtotal + $shipping + $totalTax;

        $orderNumber = 'KSP-' . date('ymd') . '-' . rand(1000, 9999);

        $order = SpiceOrder::create([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'guest_id' => $guest ? $guest->id : null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'delivery_mode' => $deliveryMode,
            'room_number' => $validated['room_number'] ?? null,
            'shipping_address_line1' => $isVilla ? ('Krishna Cottage / ' . ($validated['room_number'] ?? 'In-House Villa')) : $validated['shipping_address_line1'],
            'shipping_address_line2' => $isVilla ? 'Resort Hand Delivery' : ($validated['shipping_address_line2'] ?? null),
            'shipping_city' => $isVilla ? 'Resort Estate' : $validated['shipping_city'],
            'shipping_state' => $isVilla ? 'Kerala' : $validated['shipping_state'],
            'shipping_pincode' => $isVilla ? '685612' : $validated['shipping_pincode'],
            'shipping_country' => 'India',
            'status' => ($validated['payment_method'] === 'cod' || $validated['payment_method'] === 'charge_to_room') ? 'processing' : 'paid',
            'payment_status' => ($validated['payment_method'] === 'cod' || $validated['payment_method'] === 'charge_to_room') ? 'pending' : 'paid',
            'subtotal' => $subtotal,
            'shipping_charge' => $shipping,
            'tax_rate' => 5.00,
            'tax_amount' => $totalTax,
            'total_amount' => $total,
            'notes' => 'Customer ordered via web store. Mode: ' . strtoupper($deliveryMode) . '. Payment: ' . strtoupper($validated['payment_method']),
        ]);

        foreach ($orderItemsData as $itemRow) {
            SpiceOrderItem::create(array_merge($itemRow, ['spice_order_id' => $order->id]));
        }

        if ($isOnlinePayment) {
            Payment::create([
                'payable_type' => SpiceOrder::class,
                'payable_id' => $order->id,
                'branch_id' => null,
                'transaction_id' => $validated['razorpay_payment_id'],
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'gateway' => 'razorpay',
                'status' => 'successful',
                'gateway_response' => [
                    'razorpay_order_id' => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature' => $validated['razorpay_signature'],
                ],
                'notes' => 'Razorpay online payment settlement for ' . $orderNumber,
                'created_by' => $user->id,
            ]);
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name . ' (Spice Customer)',
            'role' => 'customer',
            'action' => 'spice_order',
            'entity_type' => 'SpiceOrder',
            'entity_id' => $order->id,
            'previous_values' => [],
            'new_values' => [
                'order_number' => $orderNumber,
                'total' => $total,
                'delivery_mode' => $deliveryMode
            ],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        // Dispatch automated operational alerts via SMTP Email & Telegram Bot
        try {
            app(\App\Services\EmailNotificationService::class)->sendSpiceOrderAlert($order);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Email Spice Order Alert failed: " . $e->getMessage());
        }
        try {
            app(\App\Services\TelegramNotificationService::class)->sendSpiceOrderAlert($order);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Telegram Spice Order Alert failed: " . $e->getMessage());
        }

        return redirect()->route('spices.index')->with('order_success', [
            'order_number' => $orderNumber,
            'total' => number_format($total, 2),
            'customer' => $validated['customer_name']
        ]);
    }

    /**
     * Preview return or cancellation refund calculation for customer order.
     */
    public function previewReturn(Request $request, int $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in.'], 401);
        }

        $user = Auth::user();
        $order = SpiceOrder::with('items')->findOrFail($id);

        $isOwner = ($order->user_id === $user->id) ||
                   ($user->guest && $order->guest_id === $user->guest->id) ||
                   ($order->customer_email === $user->email);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized order access.'], 403);
        }

        $calc = SpiceReturnRule::calculateRefund($order);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'refund_status' => $order->refund_status,
                'created_at' => $order->created_at ? $order->created_at->format('M d, Y') : '',
            ],
            'calculation' => $calc,
        ]);
    }

    /**
     * Customer submits return / cancellation request from client dashboard.
     */
    public function requestReturn(Request $request, int $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in.'], 401);
        }

        $user = Auth::user();
        $order = SpiceOrder::with('items')->findOrFail($id);

        $isOwner = ($order->user_id === $user->id) ||
                   ($user->guest && $order->guest_id === $user->guest->id) ||
                   ($order->customer_email === $user->email);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if (in_array($order->refund_status, ['requested', 'approved', 'refunded'])) {
            return response()->json(['success' => false, 'message' => 'A return request is already in progress or completed for this order.'], 422);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
            'request_type' => 'nullable|in:cancellation,return',
        ]);

        $calc = SpiceReturnRule::calculateRefund($order, $validated['request_type'] ?? null);

        if (!$calc['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $calc['rule_description'] ?? 'This product does not have return policies.',
            ], 422);
        }

        $matchedRuleId = $calc['matched_rule'] ? $calc['matched_rule']->id : null;
        $refundAmount = $calc['cashback'];

        $order->update([
            'cancellation_reason' => $validated['cancellation_reason'],
            'refund_status' => 'requested',
            'spice_return_rule_id' => $matchedRuleId,
            'refund_amount' => $refundAmount,
            'return_notes' => 'Customer requested ' . ($calc['request_type'] === 'cancellation' ? 'cancellation' : 'return') . ' under rule: ' . $calc['rule_name'],
        ]);

        // Record notification alert for estate manager
        NotificationLog::create([
            'event' => 'spice_return_requested',
            'channel' => 'sms',
            'recipient' => '+91 94471 22334',
            'reference_type' => SpiceOrder::class,
            'reference_id' => $order->id,
            'subject' => "Spice Order Return Request #{$order->order_number}",
            'message_body' => "Guest {$order->customer_name} requested a return for Spice Order #{$order->order_number} (Eligible Cashback: ₹" . number_format($refundAmount, 2) . "). Reason: {$validated['cancellation_reason']}",
            'status' => 'delivered',
            'sent_at' => Carbon::now(),
        ]);

        // Dispatch automated operational alerts via SMTP Email & Telegram Bot
        try {
            app(\App\Services\EmailNotificationService::class)->sendSpiceReturnAlert($order);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Email Spice Return Alert failed: " . $e->getMessage());
        }
        try {
            app(\App\Services\TelegramNotificationService::class)->sendSpiceReturnAlert($order);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Telegram Spice Return Alert failed: " . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => 'customer',
            'action' => 'request_spice_return',
            'entity_type' => 'SpiceOrder',
            'entity_id' => $order->id,
            'new_values' => [
                'order_number' => $order->order_number,
                'reason' => $validated['cancellation_reason'],
                'eligible_refund' => $refundAmount,
            ],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        $successMsg = ($calc['request_type'] === 'cancellation')
            ? "Your order cancellation request (#{$order->order_number}) has been submitted. Eligible refund of ₹" . number_format($refundAmount, 2) . " will be processed by our accounts team."
            : "Your return request (#{$order->order_number}) has been logged. Our plantation support desk will review and process your ₹" . number_format($refundAmount, 2) . " refund.";

        return response()->json([
            'success' => true,
            'message' => $successMsg,
            'order' => $order->fresh(['items', 'returnRule']),
            'calculation' => $calc,
        ]);
    }
}
