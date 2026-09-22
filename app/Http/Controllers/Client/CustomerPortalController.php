<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use App\Models\Guest;
use App\Models\MenuItem;
use App\Models\NearbyLocation;
use App\Models\NotificationLog;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SpiceOrder;
use App\Models\SpiceOrderItem;
use App\Models\SpiceProduct;
use App\Models\TaxiRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerPortalController extends Controller
{
    /**
     * Comprehensive Customer Dashboard with Check-In Lifecycle Filtering
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // 1. Locate or create unified CRM Guest record for authenticated user
        $guest = $user->guest ?? Guest::firstOrCreate(
            ['email' => $user->email],
            [
                'user_id' => $user->id,
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name, 2)[1] ?? '',
                'phone' => $user->phone,
                'country' => 'India',
                'vip_level' => 'standard',
            ]
        );

        if (!$guest->user_id) {
            $guest->update(['user_id' => $user->id]);
        }

        // 2. Fetch all reservations linked to this customer
        $allReservations = Reservation::with(['roomType.amenitiesList', 'room', 'branch', 'review', 'latestExtensionRequest'])
            ->where(function ($q) use ($user, $guest) {
                $q->where('guest_id', $guest->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('guest', function ($gq) use ($user) {
                      $gq->where('email', $user->email)
                         ->when($user->phone, fn($pq) => $pq->orWhere('phone', $user->phone));
                  });
            })
            ->orderBy('check_in_date', 'desc')
            ->get();

        $today = Carbon::today();

        // 3. Partition reservations into distinct lifecycle stages
        $inHouseStays = $allReservations->filter(function ($res) use ($today) {
            if ($res->status === 'checked_in') return true;
            if ($res->status === 'confirmed') {
                $ci = Carbon::parse($res->check_in_date);
                $co = Carbon::parse($res->check_out_date);
                return $today->betweenIncluded($ci, $co);
            }
            return false;
        });

        $upcomingStays = $allReservations->filter(function ($res) use ($today) {
            return $res->status === 'confirmed' && Carbon::parse($res->check_in_date)->isAfter($today);
        });

        $completedStays = $allReservations->filter(function ($res) {
            return $res->status === 'checked_out';
        });

        $otherStays = $allReservations->filter(function ($res) {
            return in_array($res->status, ['cancelled', 'no_show']);
        });

        // 4. Resolve selected stay context (via query ?stay_id= or ?code= or smart default)
        $selectedStay = null;
        $stayId = $request->query('stay_id');
        $code = $request->query('code');

        if ($stayId) {
            $selectedStay = $allReservations->firstWhere('id', (int) $stayId);
        } elseif ($code) {
            $selectedStay = $allReservations->firstWhere('booking_code', strtoupper(trim($code)));
        }

        if (!$selectedStay) {
            $selectedStay = $inHouseStays->first()
                ?? $upcomingStays->first()
                ?? $completedStays->first()
                ?? $allReservations->first();
        }

        // 5. Compute lifecycle state flags for the active view
        $isInHouse = false;
        $isUpcoming = false;
        $isCompleted = false;

        if ($selectedStay) {
            $ci = Carbon::parse($selectedStay->check_in_date);
            $co = Carbon::parse($selectedStay->check_out_date);

            $isInHouse = ($selectedStay->status === 'checked_in') ||
                         ($selectedStay->status === 'confirmed' && $today->betweenIncluded($ci, $co));

            $isUpcoming = ($selectedStay->status === 'confirmed' && $ci->isAfter($today));
            $isCompleted = ($selectedStay->status === 'checked_out');
        }

        // 6. Gather all assigned rooms across same branch (for multi-room reservations)
        $assignedRooms = [];
        $siblingStays = collect();
        if ($selectedStay) {
            $siblingStays = $allReservations->where('branch_id', $selectedStay->branch_id)
                ->where('id', '!=', $selectedStay->id)
                ->filter(fn($r) => in_array($r->status, ['checked_in', 'confirmed']));

            if ($selectedStay->room) {
                $assignedRooms[] = (string) $selectedStay->room->room_number;
            }
            foreach ($siblingStays as $sib) {
                if ($sib->room && !in_array((string) $sib->room->room_number, $assignedRooms)) {
                    $assignedRooms[] = (string) $sib->room->room_number;
                }
            }
            // If rooms_count is > 1 but no second physical room is allocated yet
            $expectedCount = max(count($assignedRooms), (int) ($selectedStay->rooms_count ?? 1));
            if (count($assignedRooms) < $expectedCount && !empty($assignedRooms)) {
                $firstNum = is_numeric($assignedRooms[0]) ? (int)$assignedRooms[0] : 101;
                while (count($assignedRooms) < $expectedCount) {
                    $assignedRooms[] = (string) ($firstNum + count($assignedRooms));
                }
            }
        }

        // 7. Contextual data based on selected stay
        $nearbyLocations = collect();
        $diningItems = collect();
        $spiceProducts = collect();
        $hasReview = false;
        $userReview = null;

        if ($selectedStay) {
            // Nearest tourist locations & attractive spots near the taken branch (available only)
            $nearbyLocations = NearbyLocation::where('branch_id', $selectedStay->branch_id)
                ->where('is_available', true)
                ->orderBy('sort_order')
                ->get();

            if ($nearbyLocations->isEmpty()) {
                $nearbyLocations = NearbyLocation::where('is_available', true)->where('is_featured', true)->take(6)->get();
            }

            // In-house dining menu items for the booked branch with fallback
            $diningItems = MenuItem::with('category')
                ->where(function ($q) use ($selectedStay) {
                    $q->where('branch_id', $selectedStay->branch_id)->orWhereNull('branch_id');
                })
                ->where('availability_state', '!=', 'hidden')
                ->orderBy('sort_order')
                ->take(12)
                ->get();

            if ($diningItems->isEmpty()) {
                $diningItems = MenuItem::with('category')
                    ->where('availability_state', '!=', 'hidden')
                    ->orderBy('sort_order')
                    ->take(12)
                    ->get();
            }

            // Popular estate spices for in-room quick ordering (Active only)
            $spiceProducts = SpiceProduct::active()
                ->orderBy('sort_order')
                ->take(12)
                ->get();

            // Review status for verified review submission
            $userReview = Review::where('reservation_id', $selectedStay->id)->first();
            $hasReview = !is_null($userReview);
        } else {
            // Fallback content when user has no reservations
            $diningItems = MenuItem::with('category')->where('availability_state', '!=', 'hidden')->take(12)->get();
            $spiceProducts = SpiceProduct::active()->orderBy('sort_order')->take(12)->get();
        }

        // 8. Order history for this customer (Dining tickets and Spice shipments)
        $foodOrders = FoodOrder::with(['items.menuItem', 'foodReview', 'branch'])
            ->where(function ($q) use ($guest, $user) {
                if ($guest) $q->where('guest_id', $guest->id);
                $q->orWhere('customer_phone', $user->phone);
            })
            ->latest()
            ->take(10)
            ->get();

        $spiceOrders = SpiceOrder::with(['items', 'returnRule'])
            ->where(function ($q) use ($guest, $user) {
                $q->where('user_id', $user->id);
                if ($guest) $q->orWhere('guest_id', $guest->id);
                $q->orWhere('customer_email', $user->email);
            })
            ->latest()
            ->take(10)
            ->get();

        // 9. All branches for browse-only guests
        $allBranches = Branch::where('status', 'active')->orderBy('sort_order')->get();

        // 10. Cancellation rules & policy for dynamic refund preview
        $cancellationRules = \App\Models\CancellationRule::where('is_active', true)->orderBy('sort_order')->get();
        $cancellationPolicy = \App\Models\Setting::get('cancellation_policy_description', 'Full refund is available up to 72 hours prior to 2:00 PM check-in.');

        // 10.5. Spices Return Rules & Policy for dashboard return requests
        $spiceReturnRules = \App\Models\SpiceReturnRule::active()->orderBy('sort_order')->get();
        $spiceReturnPolicyDescription = \App\Models\Setting::get('spice_return_policy_description', '100% full refund before dispatch. 7-day freshness return guarantee on unopened packages.');

        // 11. Bookable Resort Facilities & Experiences for in-house stay
        $bookableFacilities = collect();
        $myFacilityBookings = collect();

        if ($selectedStay) {
            $bookableFacilities = Facility::where('is_published', true)
                ->where('is_bookable', true)
                ->where(function ($q) use ($selectedStay) {
                    $q->whereNull('branch_id')->orWhere('branch_id', $selectedStay->branch_id);
                })
                ->orderBy('sort_order')
                ->get();

            $myFacilityBookings = FacilityBooking::with(['facility', 'branch'])
                ->where('reservation_id', $selectedStay->id)
                ->latest()
                ->get();

            $myTaxiRequests = TaxiRequest::with(['branch', 'folioCharge'])
                ->where('reservation_id', $selectedStay->id)
                ->latest()
                ->get();
        } else {
            $myTaxiRequests = collect();
        }

        return view('client.dashboard.index', compact(
            'user',
            'guest',
            'allReservations',
            'inHouseStays',
            'upcomingStays',
            'completedStays',
            'otherStays',
            'selectedStay',
            'isInHouse',
            'isUpcoming',
            'isCompleted',
            'assignedRooms',
            'siblingStays',
            'nearbyLocations',
            'diningItems',
            'spiceProducts',
            'hasReview',
            'userReview',
            'foodOrders',
            'spiceOrders',
            'allBranches',
            'cancellationRules',
            'cancellationPolicy',
            'spiceReturnRules',
            'spiceReturnPolicyDescription',
            'bookableFacilities',
            'myFacilityBookings',
            'myTaxiRequests'
        ));
    }

    /**
     * Backward compatibility alias for /account
     */
    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    /**
     * Handle stay extension request (increasing days to checkout)
     */
    public function extendStay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'new_checkout_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $reservation = Reservation::with(['branch', 'roomType', 'room', 'guest'])->findOrFail($validated['reservation_id']);

        // Check ownership
        $isOwner = ($reservation->created_by === $user->id) ||
                   ($user->guest && $reservation->guest_id === $user->guest->id) ||
                   ($reservation->guest && $reservation->guest->email === $user->email);

        if (!$isOwner && !$user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: This reservation does not belong to your account.'], 403);
        }

        $currentCheckout = Carbon::parse($reservation->check_out_date);
        $newCheckout = Carbon::parse($validated['new_checkout_date']);

        if (!$newCheckout->isAfter($currentCheckout)) {
            return response()->json([
                'success' => false,
                'message' => 'New check-out date must be after your current departure date (' . $currentCheckout->format('M d, Y') . ').'
            ], 422);
        }

        $extraNights = $currentCheckout->diffInDays($newCheckout);
        $rate = (float) $reservation->nightly_rate;
        $extraSubtotal = $rate * $extraNights;
        $extraTax = round($extraSubtotal * 0.12, 2); // 12% GST standard
        $extraTotal = $extraSubtotal + $extraTax;

        $roomLabel = $reservation->room ? "Room {$reservation->room->room_number}" : ($reservation->roomType ? $reservation->roomType->name : 'Villa');
        $branchName = $reservation->branch ? $reservation->branch->name : 'Resort';

        // 1. Update reservation notes with extension request
        $extensionNote = sprintf(
            "\n[EXTEND STAY REQUEST %s]: Guest requested checkout extension from %s to %s (+%d nights, est. ₹%s). Notes: %s",
            now()->format('Y-m-d H:i'),
            $currentCheckout->format('d M Y'),
            $newCheckout->format('d M Y'),
            $extraNights,
            number_format($extraTotal, 2),
            $validated['notes'] ?? 'None'
        );

        $reservation->update([
            'special_requests' => ($reservation->special_requests ?? '') . $extensionNote,
        ]);

        // 2. Automatically dispatch high-priority enquiry message in unified chat to alert branch manager
        $enquiry = Enquiry::where('reservation_id', $reservation->id)
            ->whereNotIn('status', ['closed'])
            ->latest()
            ->first();

        if (!$enquiry) {
            $ticketNumber = 'ENQ-' . date('Y') . '-' . strtoupper(Str::random(5));
            $enquiry = Enquiry::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $user->id,
                'guest_id' => $reservation->guest_id,
                'branch_id' => $reservation->branch_id,
                'reservation_id' => $reservation->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'topic' => 'booking_related',
                'subject' => "Stay Extension Request ({$roomLabel}) - {$reservation->booking_code}",
                'status' => 'new',
                'priority' => 'urgent',
                'has_unread_messages' => true,
                'last_message_at' => Carbon::now(),
            ]);
        } else {
            $enquiry->update([
                'priority' => 'urgent',
                'status' => 'new',
                'has_unread_messages' => true,
                'last_message_at' => Carbon::now(),
            ]);
        }

        // Customer message
        $guestMessageText = "🛎️ [Stay Extension Request] I would like to extend our checkout date for {$roomLabel} from {$currentCheckout->format('M d, Y')} to {$newCheckout->format('M d, Y')} (+{$extraNights} extra night" . ($extraNights > 1 ? 's' : '') . ", est. ₹" . number_format($extraTotal, 2) . "). Reason/Notes: " . ($validated['notes'] ?: 'No special requirements.');

        EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'customer',
            'user_id' => $user->id,
            'message' => $guestMessageText,
            'is_internal_note' => false,
            'target_room' => $reservation->room ? (string)$reservation->room->room_number : null,
        ]);

        // Concierge automated confirmation reply
        EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'staff',
            'user_id' => null,
            'message' => "Namaste {$user->name}! We have received your request to extend your holiday until {$newCheckout->format('M d, Y')}. Our {$branchName} branch manager is reviewing room inventory and will confirm your extended stay shortly.",
            'is_internal_note' => false,
            'target_room' => $reservation->room ? (string)$reservation->room->room_number : null,
        ]);

        // 3. Log to audit trail
        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name . ' (Customer)',
            'role' => 'customer',
            'branch_id' => $reservation->branch_id,
            'action' => 'extend_stay_request',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'previous_values' => ['check_out_date' => $currentCheckout->format('Y-m-d')],
            'new_values' => [
                'requested_check_out_date' => $newCheckout->format('Y-m-d'),
                'extra_nights' => $extraNights,
                'estimated_extra_amount' => $extraTotal,
            ],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stay extension request submitted successfully! Our {$branchName} manager will confirm within a few moments.",
            'extra_nights' => $extraNights,
            'new_checkout_date' => $newCheckout->format('D, M d, Y'),
            'estimated_extra_amount' => number_format($extraTotal, 2),
        ]);
    }

    /**
     * 1-Tap Quick In-Room Dining Order from Dashboard
     */
    public function quickOrderDining(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'nullable|exists:reservations,id',
            'branch_id' => 'nullable|exists:branches,id',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'table_number' => 'nullable|string|max:50',
            'target_room' => 'nullable|string|max:50',
            'special_instructions' => 'nullable|string|max:500',
            'payment_choice' => 'nullable|string|in:charge_to_room,upi_cod,upi,cod',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $reservation = !empty($validated['reservation_id'])
            ? Reservation::with(['branch', 'room', 'guest'])->find($validated['reservation_id'])
            : null;

        $guest = ($reservation && $reservation->guest) ? $reservation->guest : $user->guest;

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($validated['items'] as $itemInput) {
            $menuItem = MenuItem::findOrFail($itemInput['id']);
            $lineTotal = $menuItem->price * $itemInput['quantity'];
            $subtotal += $lineTotal;

            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id,
                'item_name' => $menuItem->name,
                'unit_price' => $menuItem->price,
                'quantity' => $itemInput['quantity'],
                'subtotal' => $lineTotal,
            ];
        }

        $taxAmount = round($subtotal * 0.05, 2);
        $totalAmount = $subtotal + $taxAmount;

        $orderNumber = 'KFD-' . date('ymd') . '-' . rand(100, 999);
        $assignedRoom = $validated['target_room']
            ?? $validated['table_number']
            ?? ($reservation && $reservation->room ? 'Villa ' . $reservation->room->room_number : 'In-House Villa');

        $paymentChoice = $validated['payment_choice'] ?? 'charge_to_room';
        $specialNotes = ($paymentChoice === 'charge_to_room' ? '[Charge to Villa Folio] ' : ('[' . strtoupper($paymentChoice) . '] ')) . ($validated['special_instructions'] ?? '');

        $branchId = $reservation ? $reservation->branch_id : ($validated['branch_id'] ?? 1);
        $roomId = $reservation ? $reservation->room_id : null;

        $foodOrder = FoodOrder::create([
            'order_number' => $orderNumber,
            'branch_id' => $branchId,
            'guest_id' => $guest ? $guest->id : null,
            'room_id' => $roomId,
            'customer_name' => $validated['customer_name'] ?? $user->name,
            'customer_phone' => $validated['customer_phone'] ?? ($user->phone ?? ($guest ? $guest->phone : 'In-House')),
            'order_type' => 'room_service',
            'table_number' => $assignedRoom,
            'status' => 'new',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'special_instructions' => $specialNotes,
            'ordered_at' => Carbon::now(),
        ]);

        foreach ($orderItemsData as $itemRow) {
            FoodOrderItem::create(array_merge($itemRow, ['food_order_id' => $foodOrder->id]));
        }

        $bName = ($reservation && $reservation->branch) ? $reservation->branch->name : 'Resort Central';

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name . ' (In-Room Guest)',
            'role' => 'customer',
            'branch_id' => $branchId,
            'action' => 'dashboard_room_service_order',
            'entity_type' => 'FoodOrder',
            'entity_id' => $foodOrder->id,
            'previous_values' => [],
            'new_values' => [
                'order_number' => $orderNumber,
                'room' => $assignedRoom,
                'total' => $totalAmount,
                'payment_choice' => $paymentChoice
            ],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order #{$orderNumber} transmitted to {$bName} kitchen! Delivery to {$assignedRoom}.",
            'order_number' => $orderNumber,
            'total' => number_format($totalAmount, 2),
            'room' => $assignedRoom,
        ]);
    }

    /**
     * 1-Tap Quick Farm Fresh Spice Order from Dashboard
     */
    public function quickOrderSpices(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in to order spices.'], 401);
        }

        $validated = $request->validate([
            'reservation_id' => 'nullable|exists:reservations,id',
            'target_room' => 'nullable|string|max:50',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'special_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:spice_products,id',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.weight_kg' => 'nullable|numeric|min:0.05',
            'items.*.type' => 'nullable|string|in:packet,loose',
            'delivery_mode' => 'nullable|string|in:villa_delivery,courier_home,villa,courier',
        ]);

        $user = Auth::user();
        $reservation = !empty($validated['reservation_id'])
            ? Reservation::with(['branch', 'room', 'guest'])->find($validated['reservation_id'])
            : null;
        $guest = ($reservation && $reservation->guest) ? $reservation->guest : $user->guest;

        $subtotal = 0;
        $totalTax = 0;
        $orderItemsData = [];

        foreach ($validated['items'] as $itemInput) {
            // STRICT ACTIVE-ONLY CHECK
            $product = SpiceProduct::active()->find($itemInput['id']);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more spice products are currently unavailable or inactive.'
                ], 422);
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
                $unitPrice = (float)$product->price;
                $lineTotal = round($unitPrice * $qty, 2);
                $lineTax = round($lineTotal * ($taxRate / 100), 2);
                $weightKg = ($product->weight_grams ? ($product->weight_grams * $qty / 1000) : null);
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

        $rawMode = $validated['delivery_mode'] ?? ($reservation ? 'villa' : 'courier');
        $deliveryMode = in_array($rawMode, ['villa', 'villa_delivery']) ? 'villa' : 'courier';
        $shipping = ($deliveryMode === 'villa') ? 0 : (($subtotal >= 999) ? 0 : 80);
        $total = $subtotal + $shipping + $totalTax;

        $orderNumber = 'KSP-' . date('ymd') . '-' . rand(1000, 9999);
        $assignedRoom = $validated['target_room'] ?? ($reservation && $reservation->room ? 'Villa ' . $reservation->room->room_number : 'In-House Villa');

        $branchName = $reservation && $reservation->branch ? $reservation->branch->name : 'Resort Main Branch';
        $branchCity = $reservation && $reservation->branch ? $reservation->branch->city : 'Kerala';
        $branchPin = $reservation && $reservation->branch ? ($reservation->branch->pincode ?? '685612') : '685612';

        $order = SpiceOrder::create([
            'order_number' => $orderNumber,
            'user_id' => $user->id,
            'guest_id' => $guest ? $guest->id : null,
            'customer_name' => $validated['customer_name'] ?? $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $validated['customer_phone'] ?? ($user->phone ?? ($guest ? $guest->phone : 'N/A')),
            'delivery_mode' => $deliveryMode,
            'room_number' => ($deliveryMode === 'villa') ? $assignedRoom : null,
            'shipping_address_line1' => ($deliveryMode === 'villa') ? "Deliver to {$assignedRoom} - {$branchName}" : ($guest->address ?? 'Customer Residence'),
            'shipping_city' => ($deliveryMode === 'villa') ? $branchCity : ($guest->city ?? 'Kerala'),
            'shipping_state' => 'Kerala',
            'shipping_pincode' => $branchPin,
            'shipping_country' => 'India',
            'status' => 'processing',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'shipping_charge' => $shipping,
            'tax_rate' => 5.00,
            'tax_amount' => $totalTax,
            'total_amount' => $total,
            'notes' => ($deliveryMode === 'villa') ? "Deliver directly to guest in {$assignedRoom}. Billed to Room Folio." : "Courier to registered guest address.",
        ]);

        foreach ($orderItemsData as $itemRow) {
            SpiceOrderItem::create(array_merge($itemRow, ['spice_order_id' => $order->id]));
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name . ' (Spice Guest)',
            'role' => 'customer',
            'branch_id' => $reservation ? $reservation->branch_id : null,
            'action' => 'dashboard_spice_order',
            'entity_type' => 'SpiceOrder',
            'entity_id' => $order->id,
            'previous_values' => [],
            'new_values' => ['order_number' => $orderNumber, 'total' => $total, 'mode' => $deliveryMode],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Spice order #{$orderNumber} confirmed! " . (($deliveryMode === 'villa') ? "Billed to room folio for delivery to {$assignedRoom}." : "Estate dispatch registered."),
            'order_id' => $order->id,
            'order_number' => $orderNumber,
            'total' => number_format($total, 2),
            'order' => $order->fresh(['items']),
        ]);
    }

    public function submitFoodOrderReview(Request $request, int $orderId): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please log in to submit a review.'], 401);
        }

        $order = FoodOrder::with('items')->findOrFail($orderId);

        $isOwner = ($order->guest_id && $user->guest && $order->guest_id === $user->guest->id)
            || ($order->customer_phone && $order->customer_phone === $user->phone)
            || ($order->customer_name === $user->name);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'You can only review your own dining orders.'], 403);
        }

        if ($order->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'You can review your order once it is delivered.'], 400);
        }

        if ($order->foodReview) {
            return response()->json(['success' => false, 'message' => 'You have already submitted a review for this order.'], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $primaryDish = $order->items->first()?->menu_item_id;

        $review = \App\Models\FoodReview::create([
            'food_order_id' => $order->id,
            'menu_item_id' => $primaryDish,
            'branch_id' => $order->branch_id,
            'user_id' => $user->id,
            'guest_id' => $order->guest_id,
            'customer_name' => $user->name,
            'rating' => (int)$validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your meal review has been received and submitted for manager approval.',
            'review' => $review,
        ]);
    }

    /**
     * Book a resort facility / experience from the customer dashboard (checked-in guests).
     */
    public function bookFacility(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please sign in to book resort experiences.'], 401);
        }

        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'facility_id' => 'required|exists:facilities,id',
            'booking_date' => 'required|date',
            'guests_count' => 'nullable|integer|min:1|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $reservation = Reservation::with(['room', 'branch', 'guest'])->findOrFail($validated['reservation_id']);

        // Verify ownership
        $guest = $user->guest;
        $isOwner = ($reservation->guest_id && $guest && $reservation->guest_id === $guest->id)
            || ($reservation->created_by === $user->id)
            || ($reservation->guest && $reservation->guest->email === $user->email);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized reservation access.'], 403);
        }

        // Check stay dates
        $ci = Carbon::parse($reservation->check_in_date);
        $co = Carbon::parse($reservation->check_out_date);
        $bDate = Carbon::parse($validated['booking_date']);

        if ($bDate->lt($ci) || $bDate->gt($co)) {
            return response()->json([
                'success' => false,
                'message' => "Booking date must be within your stay duration (" . $ci->format('d M Y') . " to " . $co->format('d M Y') . ")."
            ], 422);
        }

        $facility = Facility::where('is_published', true)->where('is_bookable', true)->findOrFail($validated['facility_id']);

        $guestsCount = (int)($validated['guests_count'] ?? 1);
        $rate = (float)($facility->rate ?? 0.00);
        $totalAmount = round($rate * $guestsCount, 2);

        $booking = FacilityBooking::create([
            'facility_id' => $facility->id,
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'branch_id' => $facility->branch_id ?? $reservation->branch_id,
            'booking_date' => $validated['booking_date'],
            'guests_count' => $guestsCount,
            'rate' => $rate,
            'total_amount' => $totalAmount,
            'allocated_time_slot' => null, // Manager allocates time period
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Dispatch automated operational alerts via SMTP Email & Telegram Bot
        try {
            app(\App\Services\EmailNotificationService::class)->sendFacilityBookingAlert($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Email Facility Alert failed: " . $e->getMessage());
        }
        try {
            app(\App\Services\TelegramNotificationService::class)->sendFacilityBookingAlert($booking);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Telegram Facility Alert failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Experience '{$facility->name}' booked successfully! Our concierge will schedule your time slot.",
            'booking' => $booking->load(['facility', 'branch']),
        ]);
    }

    /**
     * Cancel a facility booking from the customer dashboard.
     */
    public function cancelFacilityBooking(Request $request, int $bookingId): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        $booking = FacilityBooking::with(['reservation.guest'])->findOrFail($bookingId);
        $reservation = $booking->reservation;

        $guest = $user->guest;
        $isOwner = ($reservation && $reservation->guest_id && $guest && $reservation->guest_id === $guest->id)
            || ($reservation && $reservation->created_by === $user->id)
            || ($reservation && $reservation->guest && $reservation->guest->email === $user->email);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($booking->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Completed experiences cannot be cancelled.'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Facility booking cancelled successfully.',
            'booking' => $booking,
        ]);
    }

    /**
     * Book In-House Resort Cab / Excursion for Checked-In Guests
     */
    public function bookTaxi(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in to plan an excursion.'], 401);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string|max:50',
            'passengers_count' => 'required|integer|min:1|max:20',
            'selected_location_ids' => 'nullable|array',
            'selected_location_ids.*' => 'integer|exists:nearby_locations,id',
            'extra_locations_notes' => 'nullable|string|max:1000',
        ]);

        if (empty($validated['selected_location_ids']) && empty($validated['extra_locations_notes'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one destination or mention your custom stops.'
            ], 422);
        }

        // Validate that reservation belongs to user/guest and is active/checked-in
        $reservation = Reservation::with(['guest', 'branch', 'room'])->findOrFail($validated['reservation_id']);

        $guest = $user->guest;
        $isOwner = ($guest && $reservation->guest_id === $guest->id)
            || $reservation->created_by === $user->id
            || ($reservation->guest && $reservation->guest->email === $user->email);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized reservation access.'], 403);
        }

        $today = Carbon::today();
        $ci = Carbon::parse($reservation->check_in_date);
        $co = Carbon::parse($reservation->check_out_date);

        $canBook = ($reservation->status === 'checked_in') ||
                   ($reservation->status === 'confirmed' && $today->betweenIncluded($ci, $co));

        if (!$canBook) {
            return response()->json([
                'success' => false,
                'message' => 'Cab excursion booking is exclusively available during your active resort stay.'
            ], 422);
        }

        // Ensure pickup date is within the stay
        $pickupDate = Carbon::parse($validated['pickup_date']);
        if ($pickupDate->isBefore($ci) || $pickupDate->isAfter($co)) {
            return response()->json([
                'success' => false,
                'message' => "Pickup date must be during your stay ({$ci->format('M d')} to {$co->format('M d')})."
            ], 422);
        }

        $ref = 'TAX-' . strtoupper(Str::random(6));

        $taxi = TaxiRequest::create([
            'booking_reference' => $ref,
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'branch_id' => $reservation->branch_id,
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
            'passengers_count' => $validated['passengers_count'],
            'selected_location_ids' => $validated['selected_location_ids'] ?? [],
            'extra_locations_notes' => $validated['extra_locations_notes'] ?? null,
            'status' => 'pending',
        ]);

        // Alert concierge / manager via NotificationLog
        NotificationLog::create([
            'event' => 'taxi_excursion_requested',
            'channel' => 'sms',
            'recipient' => $reservation->branch ? ($reservation->branch->phone ?: '+91 94471 22334') : '+91 98765 43210',
            'branch_id' => $reservation->branch_id,
            'reference_type' => TaxiRequest::class,
            'reference_id' => $taxi->id,
            'subject' => "New Cab Excursion Request #{$ref}",
            'message_body' => "Guest {$reservation->guest->full_name} (Villa " . ($reservation->room ? $reservation->room->room_number : '101') . ") requested a cab excursion for {$pickupDate->format('M d')} at {$validated['pickup_time']}. Stops: " . ($taxi->selected_locations->pluck('name')->implode(', ') ?: 'Custom Route') . ". Please contact guest with fare.",
            'status' => 'delivered',
            'sent_at' => Carbon::now(),
        ]);

        // Dispatch automated operational alerts via SMTP Email & Telegram Bot
        try {
            app(\App\Services\EmailNotificationService::class)->sendTaxiRequestAlert($taxi);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Email Taxi Alert failed: " . $e->getMessage());
        }
        try {
            app(\App\Services\TelegramNotificationService::class)->sendTaxiRequestAlert($taxi);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Telegram Taxi Alert failed: " . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $reservation->guest ? $reservation->guest->full_name : $user->name,
            'role' => 'customer',
            'branch_id' => $reservation->branch_id,
            'action' => 'book_taxi_request',
            'entity_type' => 'TaxiRequest',
            'entity_id' => $taxi->id,
            'new_values' => $taxi->toArray(),
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Your excursion cab request (#{$ref}) has been submitted! Our concierge team will contact you shortly with vehicle details and fare quote.",
            'taxi_request' => $taxi->load('branch'),
        ]);
    }

    /**
     * Cancel an active pending taxi request
     */
    public function cancelTaxi(Request $request, int $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in.'], 401);
        }

        $user = Auth::user();
        $taxi = TaxiRequest::with('reservation.guest')->findOrFail($id);

        $guest = $user->guest;
        $isOwner = ($guest && $taxi->guest_id === $guest->id)
            || ($taxi->reservation && $taxi->reservation->created_by === $user->id)
            || ($taxi->reservation && $taxi->reservation->guest && $taxi->reservation->guest->email === $user->email);

        if (!$isOwner) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if (in_array($taxi->status, ['completed', 'cancelled'])) {
            return response()->json(['success' => false, 'message' => 'This request cannot be cancelled.'], 422);
        }

        $taxi->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => "Cab request #{$taxi->booking_reference} has been cancelled.",
        ]);
    }
}
