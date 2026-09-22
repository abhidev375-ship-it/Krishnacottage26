<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientDiningController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        $branchId = $request->query('branch_id');
        $categoryId = $request->query('category_id');
        $dietary = $request->query('dietary'); // veg, non_veg

        $categories = MenuCategory::where('is_active', true)
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderBy('sort_order')
            ->get();

        $query = MenuItem::with(['category', 'branch', 'approvedReviews'])
            ->where('availability_state', '!=', 'hidden');

        if ($branchId) {
            $query->where(function($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhere('is_all_branches', true)
                  ->orWhereNull('branch_id')
                  ->orWhereJsonContains('allocated_branch_ids', (int)$branchId)
                  ->orWhereJsonContains('allocated_branch_ids', (string)$branchId);
            });
        }

        if ($categoryId) {
            $query->where('menu_category_id', $categoryId);
        }

        if ($dietary === 'veg') {
            $query->where('is_vegetarian', true);
        }

        $items = $query->orderBy('sort_order')->get();

        // Check if current authenticated user has an active in-house stay
        $isInHouse = false;
        $inHouseStay = null;
        if (Auth::check()) {
            $user = Auth::user();
            $inHouseStay = \App\Models\Reservation::with(['branch', 'room'])
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('guest_id', $user->guest ? $user->guest->id : 0)
                      ->orWhereHas('guest', function ($gq) use ($user) {
                          $gq->where('email', $user->email)
                             ->when($user->phone, fn($pq) => $pq->orWhere('phone', $user->phone));
                      });
                })
                ->where('status', 'checked_in')
                ->where('check_out_date', '>=', Carbon::today())
                ->first();
            $isInHouse = !is_null($inHouseStay);
        }

        // If in-house rooms are selectable
        $rooms = Room::where('operational_status', 'occupied')->orWhere('operational_status', 'reserved')->get();

        return view('client.dining.index', compact(
            'categories',
            'items',
            'branches',
            'branchId',
            'categoryId',
            'dietary',
            'rooms',
            'isInHouse',
            'inHouseStay'
        ));
    }

    public function placeOrder(Request $request): JsonResponse
    {
        // Enforce dining order authentication gate
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'login_url' => route('login', ['redirect' => route('dining.index')]),
                'message' => 'Please sign in to your customer account to place dining orders.'
            ], 401);
        }

        $user = Auth::user();
        $guest = $user->guest ?? \App\Models\Guest::where('email', $user->email)->orWhere('phone', $user->phone)->first();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:30',
            'branch_id' => 'nullable|exists:branches,id',
            'order_type' => 'required|in:room_service,dine_in,takeaway',
            'table_number' => 'nullable|string|max:50',
            'special_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validated['items'] as $itemInput) {
            $menuItem = MenuItem::findOrFail($itemInput['id']);
            $itemTotal = $menuItem->price * $itemInput['quantity'];
            $subtotal += $itemTotal;

            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id,
                'item_name' => $menuItem->name,
                'unit_price' => $menuItem->price,
                'quantity' => $itemInput['quantity'],
                'subtotal' => $itemTotal,
            ];
        }

        $taxAmount = round($subtotal * 0.05, 2); // 5% GST on dining
        $totalAmount = $subtotal + $taxAmount;

        $orderNumber = 'KFD-' . date('ymd') . '-' . rand(100, 999);

        $foodOrder = FoodOrder::create([
            'order_number' => $orderNumber,
            'branch_id' => $validated['branch_id'] ?? 1,
            'guest_id' => $guest ? $guest->id : null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'order_type' => $validated['order_type'],
            'table_number' => $validated['table_number'] ?? null,
            'status' => 'new',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'special_instructions' => $validated['special_instructions'] ?? null,
            'ordered_at' => Carbon::now(),
        ]);

        foreach ($orderItemsData as $itemRow) {
            FoodOrderItem::create(array_merge($itemRow, ['food_order_id' => $foodOrder->id]));
        }

        AuditLog::create([
            'user_id' => 1,
            'user_name' => $validated['customer_name'] . ' (Dining Guest)',
            'role' => 'customer',
            'branch_id' => $foodOrder->branch_id,
            'action' => 'food_order',
            'entity_type' => 'FoodOrder',
            'entity_id' => $foodOrder->id,
            'previous_values' => [],
            'new_values' => [
                'order_number' => $orderNumber,
                'total' => $totalAmount,
                'type' => $validated['order_type'],
            ],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        // Dispatch automated operational alerts via SMTP Email & Telegram Bot
        try {
            app(\App\Services\EmailNotificationService::class)->sendFoodOrderAlert($foodOrder);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Email Food Order Alert failed: " . $e->getMessage());
        }
        try {
            app(\App\Services\TelegramNotificationService::class)->sendFoodOrderAlert($foodOrder);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Telegram Food Order Alert failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$orderNumber} placed successfully! Kitchen has received your request.",
            'order_number' => $orderNumber,
            'total' => number_format($totalAmount, 2),
        ]);
    }
}
