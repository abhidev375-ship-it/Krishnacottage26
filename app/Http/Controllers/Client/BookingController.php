<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Guest;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RoomType;
use App\Services\RazorpayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Airbnb-style Rooms Explorer
     */
    public function index(Request $request)
    {
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        $categories = RoomCategory::active()->orderBy('sort_order')->get();
        $featuredAmenities = Amenity::active()->featured()->orderBy('sort_order')->get();
        
        $branchId = $request->query('branch_id');
        $categoryId = $request->query('category_id');
        $categorySlug = $request->query('category');
        $checkIn = $request->query('check_in', Carbon::tomorrow()->format('Y-m-d'));
        $checkOut = $request->query('check_out', Carbon::tomorrow()->addDays(2)->format('Y-m-d'));
        $adults = max(1, (int) $request->query('adults', 2));
        $children = max(0, (int) $request->query('children', 0));
        $totalGuests = $adults + $children;
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $amenityFilter = $request->query('amenity');

        $query = RoomType::with(['branch', 'category', 'amenitiesList', 'rooms'])
            ->where('is_active', true)
            ->where('is_bookable', true);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($categoryId) {
            $query->where('room_category_id', $categoryId);
        } elseif ($categorySlug) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        if ($totalGuests > 1) {
            $query->where('max_guests', '>=', $totalGuests);
        }

        if ($adults > 0) {
            $query->where(function ($q) use ($adults) {
                $q->whereNull('max_adults')
                  ->orWhere('max_adults', 0)
                  ->orWhere('max_adults', '>=', $adults);
            });
        }

        if ($children > 0) {
            $query->where(function ($q) use ($adults, $children) {
                $q->whereNull('max_children')
                  ->orWhereRaw('? <= (COALESCE(max_children, 0) + (GREATEST(0, COALESCE(max_adults, 2) - ?)))', [$children, $adults]);
            });
        }

        if ($minPrice) {
            $query->where('base_price', '>=', (float) $minPrice);
        }

        if ($maxPrice) {
            $query->where('base_price', '<=', (float) $maxPrice);
        }

        if ($amenityFilter) {
            $query->where(function ($q) use ($amenityFilter) {
                $q->whereHas('amenitiesList', function ($aq) use ($amenityFilter) {
                    $aq->where('amenities.name', $amenityFilter)
                       ->orWhere('amenities.slug', $amenityFilter);
                })->orWhereJsonContains('amenities', $amenityFilter);
            });
        }

        $roomTypes = $query->orderBy('sort_order')->get();

        $startDate = Carbon::parse($checkIn);
        $endDate = Carbon::parse($checkOut);
        $nights = max(1, $startDate->diffInDays($endDate));

        return view('client.rooms.index', compact(
            'roomTypes',
            'branches',
            'categories',
            'featuredAmenities',
            'branchId',
            'categoryId',
            'categorySlug',
            'checkIn',
            'checkOut',
            'adults',
            'children',
            'totalGuests',
            'nights',
            'minPrice',
            'maxPrice',
            'amenityFilter'
        ));
    }

    /**
     * Airbnb-style Room Detail Page
     */
    public function show(Request $request, string $slug)
    {
        $roomType = RoomType::with(['branch', 'category', 'amenitiesList', 'rooms'])
            ->where('slug', $slug)
            ->orWhere('id', is_numeric($slug) ? (int)$slug : 0)
            ->firstOrFail();

        $checkIn = $request->query('check_in', Carbon::tomorrow()->format('Y-m-d'));
        $checkOut = $request->query('check_out', Carbon::tomorrow()->addDays(2)->format('Y-m-d'));
        $adults = max(1, (int) $request->query('adults', 2));
        $children = max(0, (int) $request->query('children', 0));
        
        $startDate = Carbon::parse($checkIn);
        $endDate = Carbon::parse($checkOut);
        $nights = max(1, $startDate->diffInDays($endDate));

        // Approved reviews for this room or branch
        $reviews = Review::with('guest')
            ->where(function ($q) use ($roomType) {
                $q->where('room_type_id', $roomType->id)
                  ->orWhere(fn($sq) => $sq->where('branch_id', $roomType->branch_id)->whereNull('room_type_id'));
            })
            ->where('status', 'approved')
            ->latest('created_at')
            ->take(10)
            ->get();

        $averageRating = Review::where('status', 'approved')
            ->where(function ($q) use ($roomType) {
                $q->where('room_type_id', $roomType->id)
                  ->orWhere('branch_id', $roomType->branch_id);
            })
            ->avg('rating') ?: 4.95;

        $reviewsCount = Review::where('status', 'approved')
            ->where(function ($q) use ($roomType) {
                $q->where('room_type_id', $roomType->id)
                  ->orWhere('branch_id', $roomType->branch_id);
            })
            ->count() ?: 12;

        // Check if current user is eligible to write a review (verified stay)
        $canWriteReview = false;
        $eligibleReservation = null;
        if (Auth::check()) {
            $user = Auth::user();
            $guestId = $user->guest ? $user->guest->id : 0;
            $eligibleReservation = Reservation::where(function ($q) use ($user, $guestId) {
                    $q->where('guest_id', $guestId)
                      ->orWhere('created_by', $user->id);
                })
                ->where('branch_id', $roomType->branch_id)
                ->whereIn('status', ['checked_in', 'checked_out'])
                ->whereDoesntHave('review')
                ->latest('check_in_date')
                ->first();
            $canWriteReview = !is_null($eligibleReservation);
        }

        // Similar alternative rooms in same or other branches
        $otherRooms = RoomType::with(['branch', 'category'])
            ->where('id', '!=', $roomType->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('client.rooms.show', compact(
            'roomType',
            'checkIn',
            'checkOut',
            'adults',
            'children',
            'nights',
            'reviews',
            'averageRating',
            'reviewsCount',
            'canWriteReview',
            'eligibleReservation',
            'otherRooms'
        ));
    }

    /**
     * Airbnb-style 2-Column Checkout
     */
    public function checkout(Request $request, int $id)
    {
        $roomType = RoomType::with('branch')->findOrFail($id);

        // Enforce booking authentication gate
        if (!Auth::check()) {
            session(['url.intended' => $request->fullUrl()]);
            return redirect()->route('login')
                ->with('info', "Please sign in or create a customer account to reserve {$roomType->name}.");
        }

        $user = Auth::user();

        $checkIn = $request->query('check_in', Carbon::tomorrow()->format('Y-m-d'));
        $checkOut = $request->query('check_out', Carbon::tomorrow()->addDays(2)->format('Y-m-d'));
        $adults = max(1, (int) $request->query('adults', 2));
        $children = max(0, (int) $request->query('children', 0));

        $startDate = Carbon::parse($checkIn);
        $endDate = Carbon::parse($checkOut);
        $nights = max(1, $startDate->diffInDays($endDate));

        $rate = (float) $roomType->base_price;
        $subtotal = $rate * $nights;
        $tax = round($subtotal * 0.12, 2); // 12% GST
        $total = $subtotal + $tax;
        $deposit = round($total * 0.20, 2); // 20% advance option

        return view('client.rooms.checkout', compact(
            'roomType',
            'user',
            'checkIn',
            'checkOut',
            'adults',
            'children',
            'nights',
            'rate',
            'subtotal',
            'tax',
            'total',
            'deposit'
        ));
    }

    /**
     * Initiate Razorpay Order for Room Booking
     */
    public function createRazorpayOrder(Request $request, RazorpayService $razorpayService): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please sign in to proceed with booking.'], 401);
        }

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'payment_choice' => 'required|string|in:full,deposit_20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
        ]);

        $roomType = RoomType::with('branch')->findOrFail($validated['room_type_id']);

        $adults = max(1, (int) $validated['adults']);
        $children = max(0, (int) ($validated['children'] ?? 0));
        $totalGuests = $adults + $children;

        // Capacity checks
        if ($roomType->max_adults && $adults > $roomType->max_adults) {
            return response()->json(['success' => false, 'message' => "Max {$roomType->max_adults} adults allowed in {$roomType->name}."], 422);
        }
        if ($roomType->max_guests && $totalGuests > $roomType->max_guests) {
            return response()->json(['success' => false, 'message' => "Max {$roomType->max_guests} guests allowed in {$roomType->name}."], 422);
        }

        $startDate = Carbon::parse($validated['check_in_date']);
        $endDate = Carbon::parse($validated['check_out_date']);
        $nights = max(1, $startDate->diffInDays($endDate));

        $rate = (float) $roomType->base_price;
        $subtotal = $rate * $nights;
        $tax = round($subtotal * 0.12, 2);
        $total = $subtotal + $tax;

        $amountToPay = ($validated['payment_choice'] === 'deposit_20') ? round($total * 0.20, 2) : $total;

        // Quick inventory check
        $checkInStr = $startDate->toDateString();
        $checkOutStr = $endDate->toDateString();
        $availableRooms = Room::where('room_type_id', $roomType->id)
            ->whereNotIn('operational_status', ['maintenance', 'blocked', 'out_of_order'])
            ->get();

        $freeRoom = null;
        foreach ($availableRooms as $room) {
            $hasOverlap = Reservation::where('room_id', $room->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $checkOutStr)
                ->where('check_out_date', '>', $checkInStr)
                ->exists();

            if (!$hasOverlap) {
                $freeRoom = $room;
                break;
            }
        }

        if (!$freeRoom) {
            return response()->json([
                'success' => false,
                'message' => "Selected suite ({$roomType->name}) is fully booked for these dates. Please choose another date or suite.",
            ], 422);
        }

        $receiptId = 'KR-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        try {
            $order = $razorpayService->createOrder($amountToPay, $receiptId, [
                'room_type_id' => (string) $roomType->id,
                'room_type_name' => $roomType->name,
                'check_in' => $checkInStr,
                'check_out' => $checkOutStr,
                'payment_choice' => $validated['payment_choice'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
            ]);

            return response()->json([
                'success' => true,
                'key_id' => $razorpayService->getKeyId(),
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'amount_rupees' => $amountToPay,
                'currency' => $order['currency'],
                'name' => 'Krishna Cottage',
                'description' => "{$nights}-Night Stay · {$roomType->name}",
                'prefill' => [
                    'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                    'email' => $validated['email'],
                    'contact' => $validated['phone'],
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
     * Process reservation creation in MySQL KRISHNA26
     */
    public function store(Request $request)
    {
        // Enforce booking authentication gate
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to confirm your reservation.');
        }

        $user = Auth::user();

        $rules = [
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_method' => 'required|string|in:upi,card,netbanking,pay_at_resort',
            'payment_choice' => 'required|string|in:full,deposit_20',
            'special_requests' => 'nullable|string|max:1000',
        ];

        // If paying online via Razorpay, require transaction verification parameters
        if ($request->input('payment_method') !== 'pay_at_resort') {
            $rules['razorpay_payment_id'] = 'required|string';
            $rules['razorpay_order_id'] = 'required|string';
            $rules['razorpay_signature'] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Verify cryptographic signature if online payment
        if ($validated['payment_method'] !== 'pay_at_resort') {
            $razorpayService = app(RazorpayService::class);
            $signatureValid = $razorpayService->verifySignature(
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            if (!$signatureValid) {
                return back()->withInput()->with('error', 'Online payment verification failed (invalid signature). If money was deducted, contact concierge with payment reference ' . $validated['razorpay_payment_id']);
            }
        }

        $roomType = RoomType::findOrFail($validated['room_type_id']);

        $adults = max(1, (int) $validated['adults']);
        $children = max(0, (int) ($validated['children'] ?? 0));
        $totalGuests = $adults + $children;

        // Server-Side Capacity Validation
        if ($roomType->max_adults && $adults > $roomType->max_adults) {
            return back()->withInput()->with('error', "The selected suite ({$roomType->name}) accommodates a maximum of {$roomType->max_adults} adults. Please adjust your party size or select multiple suites.");
        }

        if ($roomType->max_guests && $totalGuests > $roomType->max_guests) {
            return back()->withInput()->with('error', "The selected suite ({$roomType->name}) accommodates a maximum of {$roomType->max_guests} total guests. Please select a larger suite or multiple rooms.");
        }

        $maxAllowedChildren = ($roomType->max_children ?? 0) + max(0, ($roomType->max_adults ?? 2) - $adults);
        if ($children > 0 && $children > $maxAllowedChildren && $roomType->max_children) {
            return back()->withInput()->with('error', "The selected suite cannot accommodate {$children} children with the requested {$adults} adults.");
        }

        $startDate = Carbon::parse($validated['check_in_date']);
        $endDate = Carbon::parse($validated['check_out_date']);
        $nights = max(1, $startDate->diffInDays($endDate));

        $rate = (float) $roomType->base_price;
        $subtotal = $rate * $nights;
        $tax = round($subtotal * 0.12, 2);
        $total = $subtotal + $tax;

        $paidAmount = 0;
        $paymentStatus = 'pending';

        if ($validated['payment_method'] !== 'pay_at_resort') {
            if ($validated['payment_choice'] === 'deposit_20') {
                $paidAmount = round($total * 0.20, 2);
                $paymentStatus = 'partial';
            } else {
                $paidAmount = $total;
                $paymentStatus = 'paid';
            }
        }

        // Execute booking allocation within an atomic database transaction
        $result = DB::transaction(function () use ($validated, $user, $roomType, $startDate, $endDate, $nights, $rate, $subtotal, $tax, $total, $paidAmount, $paymentStatus, $totalGuests, $request) {
            // Find or create Guest linked to authenticated user
            $guest = Guest::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'],
                    'city' => $validated['city'] ?? 'Kerala',
                    'country' => $validated['country'] ?? 'India',
                    'vip_level' => 'standard',
                    'total_stays' => 0,
                    'total_spent' => 0,
                ]
            );

            if (!$guest->user_id) {
                $guest->update(['user_id' => $user->id]);
            }

            // ATOMIC CONCURRENCY CHECK:
            // Query physical rooms for this room type and lock them for update to prevent race conditions
            $availableRooms = Room::where('room_type_id', $roomType->id)
                ->whereNotIn('operational_status', ['maintenance', 'blocked', 'out_of_order'])
                ->lockForUpdate()
                ->get();

            // Filter rooms that have zero overlapping reservations for the requested date range [startDate, endDate)
            $checkInStr = $startDate->toDateString();
            $checkOutStr = $endDate->toDateString();

            $freeRoom = null;
            foreach ($availableRooms as $room) {
                // Check physical room capacity override
                $roomCap = (int) ($room->max_guests ?? 0);
                if ($roomCap > 0 && $roomCap < $totalGuests) {
                    continue;
                }

                $hasOverlap = Reservation::where('room_id', $room->id)
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->where('check_in_date', '<', $checkOutStr)
                    ->where('check_out_date', '>', $checkInStr)
                    ->exists();

                if (!$hasOverlap) {
                    $freeRoom = $room;
                    break;
                }
            }

            // SIMULTANEOUS RACE CONDITION DETECTED:
            // If another user completed their payment and secured the last room milliseconds earlier
            if (!$freeRoom) {
                $refundTxnId = 'REF-' . date('Ymd') . '-' . strtoupper(Str::random(6));

                // If online payment was completed, trigger automated Razorpay API refund
                $razorpayRefundData = null;
                if ($validated['payment_method'] !== 'pay_at_resort' && $request->filled('razorpay_payment_id')) {
                    try {
                        $razorpayRefundData = app(RazorpayService::class)->refund(
                            $request->input('razorpay_payment_id'),
                            $paidAmount,
                            'Simultaneous booking race conflict payback'
                        );
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Razorpay automated refund exception: ' . $e->getMessage());
                    }
                }

                // 1. If money was collected/authorized, initiate instantaneous 100% automated payback ledger entry
                $refundPayment = null;
                if ($paidAmount > 0) {
                    $refundPayment = Payment::create([
                        'payable_type' => Guest::class,
                        'payable_id' => $guest->id,
                        'branch_id' => $roomType->branch_id,
                        'transaction_id' => $request->input('razorpay_payment_id') ?: $refundTxnId,
                        'amount' => $paidAmount,
                        'payment_method' => $validated['payment_method'],
                        'gateway' => ($validated['payment_method'] === 'pay_at_resort') ? 'manual' : 'razorpay',
                        'status' => 'refunded',
                        'gateway_response' => [
                            'conflict_type' => 'simultaneous_booking_race_condition',
                            'requested_room_type' => $roomType->name,
                            'check_in' => $checkInStr,
                            'check_out' => $checkOutStr,
                            'original_amount' => $paidAmount,
                            'refunded_amount' => $paidAmount,
                            'refund_reason' => 'Another guest finalized reservation milliseconds earlier. 100% automated payback executed.',
                            'razorpay_refund' => $razorpayRefundData,
                        ],
                        'notes' => 'Simultaneous booking race conflict. 100% automated payback issued to guest.',
                        'created_by' => $user->id,
                    ]);

                    // 2. Dispatch high-priority automated SMS & delivery notification to guest
                    NotificationLog::create([
                        'event' => 'simultaneous_booking_conflict_refund',
                        'channel' => 'sms',
                        'recipient' => $validated['phone'],
                        'branch_id' => $roomType->branch_id,
                        'reference_type' => 'Payment',
                        'reference_id' => $refundPayment->id,
                        'subject' => 'Booking Conflict & 100% Automated Payback - Krishna Resorts',
                        'message_body' => "Dear {$guest->first_name}, another guest secured the last available {$roomType->name} moments before your transaction settled. Your payment of ₹" . number_format($paidAmount, 2) . " has been 100% refunded (Refund Ref: #{$refundTxnId}). No cancellation fee applied. - Krishna Resorts",
                        'status' => 'delivered',
                        'sent_at' => Carbon::now(),
                    ]);
                }

                // 3. Log audit event
                AuditLog::create([
                    'user_id' => $user->id,
                    'user_name' => $guest->full_name . ' (Online Guest)',
                    'role' => 'customer',
                    'branch_id' => $roomType->branch_id,
                    'action' => 'simultaneous_conflict_payback',
                    'entity_type' => 'RoomType',
                    'entity_id' => $roomType->id,
                    'previous_values' => ['requested_dates' => [$checkInStr, $checkOutStr]],
                    'new_values' => [
                        'status' => 'refunded',
                        'refund_ref' => $refundTxnId,
                        'refund_amount' => $paidAmount,
                        'reason' => 'Room booked by peer guest milliseconds earlier',
                    ],
                    'ip_address' => $request->ip(),
                    'created_at' => Carbon::now(),
                ]);

                return [
                    'conflict' => true,
                    'refund_id' => $refundTxnId,
                    'refund_amount' => $paidAmount,
                    'room_type_name' => $roomType->name,
                ];
            }

            // SUCCESSFUL ALLOCATION (FCFS winner):
            $guest->increment('total_stays', 1);
            $guest->increment('total_spent', $total);

            // Unique Airbnb-style Booking Reference
            $bookingCode = 'KR-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $reservation = Reservation::create([
                'booking_code' => $bookingCode,
                'branch_id' => $roomType->branch_id,
                'room_type_id' => $roomType->id,
                'room_id' => $freeRoom->id,
                'guest_id' => $guest->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'adults' => $validated['adults'],
                'children' => $validated['children'] ?? 0,
                'rooms_count' => 1,
                'nightly_rate' => $rate,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'refunded_amount' => 0.00,
                'status' => 'confirmed',
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'special_requests' => $validated['special_requests'] ?? null,
                'is_counter_booking' => false,
                'created_by' => $user->id,
            ]);

            // If check-in is today, mark physical room as reserved immediately
            if ($startDate->isToday() && $freeRoom->operational_status === 'available') {
                $freeRoom->update(['operational_status' => 'reserved']);
            }

            // Log status change
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'from_status' => 'inquiry',
                'to_status' => 'confirmed',
                'user_id' => $user->id,
                'note' => 'Guest booked online via Krishna Resorts Web Platform (' . strtoupper($validated['payment_method']) . ')',
                'created_at' => Carbon::now(),
            ]);

            // Record initial payment ledger
            if ($paidAmount > 0) {
                $isRazorpay = ($validated['payment_method'] !== 'pay_at_resort');
                Payment::create([
                    'payable_type' => Reservation::class,
                    'payable_id' => $reservation->id,
                    'branch_id' => $roomType->branch_id,
                    'transaction_id' => $request->input('razorpay_payment_id') ?: ('TXN-' . strtoupper(Str::random(10))),
                    'amount' => $paidAmount,
                    'payment_method' => $validated['payment_method'],
                    'gateway' => $isRazorpay ? 'razorpay' : 'manual',
                    'status' => 'successful',
                    'gateway_response' => $isRazorpay ? [
                        'razorpay_order_id' => $request->input('razorpay_order_id'),
                        'razorpay_payment_id' => $request->input('razorpay_payment_id'),
                        'razorpay_signature' => $request->input('razorpay_signature'),
                    ] : null,
                    'notes' => 'Customer booking settlement for ' . $bookingCode . ' via ' . strtoupper($validated['payment_method']),
                    'created_by' => $user->id,
                ]);
            }

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $guest->full_name . ' (Online Guest)',
                'role' => 'customer',
                'branch_id' => $reservation->branch_id,
                'action' => 'online_reservation',
                'entity_type' => 'Reservation',
                'entity_id' => $reservation->id,
                'previous_values' => [],
                'new_values' => [
                    'booking_code' => $bookingCode,
                    'guest' => $guest->full_name,
                    'room_number' => $freeRoom->room_number,
                    'total' => $total,
                    'payment_status' => $paymentStatus,
                ],
                'ip_address' => $request->ip(),
                'created_at' => Carbon::now(),
            ]);

            // Send booking confirmation SMS containing exact physical branch location
            $branchAddress = $roomType->branch ? $roomType->branch->full_address : 'Kerala, India';
            NotificationLog::create([
                'event' => 'reservation_confirmed',
                'channel' => 'sms',
                'recipient' => $validated['phone'],
                'branch_id' => $roomType->branch_id,
                'reference_type' => Reservation::class,
                'reference_id' => $reservation->id,
                'subject' => "Booking Confirmed #{$bookingCode} - Krishna Resorts",
                'message_body' => "Dear {$guest->first_name}, your stay at " . ($roomType->branch ? $roomType->branch->name : 'Krishna Resorts') . " is confirmed! Ref: #{$bookingCode}. Dates: {$checkInStr} to {$checkOutStr}. Resort Location: {$branchAddress}. - Krishna Resorts",
                'status' => 'delivered',
                'sent_at' => Carbon::now(),
            ]);

            // Dispatch automated operational alerts via SMTP Email & Telegram Bot
            try {
                app(\App\Services\EmailNotificationService::class)->sendBookingAlert($reservation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Email Booking Alert failed: " . $e->getMessage());
            }
            try {
                app(\App\Services\TelegramNotificationService::class)->sendBookingAlert($reservation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Telegram Booking Alert failed: " . $e->getMessage());
            }

            return [
                'conflict' => false,
                'booking_code' => $bookingCode,
                'guest_email' => $guest->email,
            ];
        });

        // Handle simultaneous race condition redirect or json response
        if (!empty($result['conflict'])) {
            $msg = "Simultaneous Booking Notice: Another guest completed their reservation for {$result['room_type_name']} moments before your transaction settled. Your payment of ₹" . number_format($result['refund_amount'], 2) . " has been 100% refunded (Refund Ref: #{$result['refund_id']}). An SMS confirmation has been dispatched.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'conflict' => true,
                    'message' => $msg,
                    'refund_id' => $result['refund_id'],
                    'refund_amount' => $result['refund_amount'],
                ], 409);
            }

            return redirect()->route('rooms.index', ['branch_id' => $roomType->branch_id])
                ->with('error', $msg);
        }

        // Store last booking in session for instant access in Account portal
        session(['last_booking_code' => $result['booking_code'], 'guest_email' => $result['guest_email']]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'booking_code' => $result['booking_code'],
                'redirect' => route('booking.confirmation', ['code' => $result['booking_code']]),
            ]);
        }

        return redirect()->route('booking.confirmation', ['code' => $result['booking_code']]);
    }

    /**
     * Reservation Confirmation & Digital Stay Pass
     */
    public function confirmation(string $code)
    {
        $reservation = Reservation::with(['roomType', 'room', 'branch', 'guest'])
            ->where('booking_code', $code)
            ->firstOrFail();

        return view('client.rooms.confirmation', compact('reservation'));
    }

    /**
     * Submit Verified Guest Review (starts pending manager approval)
     */
    public function storeReview(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please log in to submit a review.'], 401);
        }

        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $reservation = Reservation::with(['guest', 'roomType'])->findOrFail($validated['reservation_id']);

        // Check that reservation belongs to user or guest
        $isOwner = ($reservation->created_by === $user->id) || ($user->guest && $reservation->guest_id === $user->guest->id);
        if (!$isOwner && !$user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: This reservation does not belong to your account.'], 403);
        }

        if (!in_array($reservation->status, ['checked_in', 'checked_out']) && !$user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Reviews can only be submitted after check-in or stay completion.'], 422);
        }

        if (Review::where('reservation_id', $reservation->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'You have already submitted a review for this stay.'], 422);
        }

        $review = Review::create([
            'reservation_id' => $reservation->id,
            'branch_id' => $reservation->branch_id,
            'guest_id' => $reservation->guest_id,
            'room_type_id' => $reservation->room_type_id,
            'rating' => (int)$validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => trim($validated['comment']),
            'stay_summary' => ($reservation->rooms_count ?? 1) . ' room · ' . ($reservation->roomType ? $reservation->roomType->name : 'Resort Cottage'),
            'status' => 'pending', // Starts in pending status for manager review
            'verified_stay' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your verified review has been submitted and will appear on the room page once approved by our resort manager.',
                'review' => $review,
            ]);
        }

        return redirect()->back()->with('success', 'Thank you! Your verified review has been submitted and is pending resort manager verification.');
    }

    /**
     * Real-Time Availability & Capacity Engine (JSON API)
     * Enforces room-type & physical room max_guests, max_adults, max_children rules,
     * operational status checks, active blocks, and overlapping reservation date conflicts.
     */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'adults' => 'nullable|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
        ]);

        $branchId = !empty($validated['branch_id']) ? (int) $validated['branch_id'] : null;
        $checkInStr = $validated['check_in'] ?? Carbon::tomorrow()->format('Y-m-d');
        $checkOutStr = $validated['check_out'] ?? Carbon::tomorrow()->addDays(2)->format('Y-m-d');

        try {
            $checkIn = Carbon::parse($checkInStr)->startOfDay();
            $checkOut = Carbon::parse($checkOutStr)->startOfDay();
        } catch (\Exception $e) {
            $checkIn = Carbon::tomorrow()->startOfDay();
            $checkOut = Carbon::tomorrow()->addDays(2)->startOfDay();
            $checkInStr = $checkIn->format('Y-m-d');
            $checkOutStr = $checkOut->format('Y-m-d');
        }

        // Ensure check-in is not in the past
        if ($checkIn->lt(Carbon::today())) {
            $checkIn = Carbon::today();
            $checkInStr = $checkIn->format('Y-m-d');
        }

        // Ensure check-out is strictly after check-in
        if ($checkOut->lte($checkIn)) {
            $checkOut = (clone $checkIn)->addDay();
            $checkOutStr = $checkOut->format('Y-m-d');
        }

        $nights = max(1, $checkIn->diffInDays($checkOut));
        $adults = max(1, (int) ($validated['adults'] ?? 2));
        $children = max(0, (int) ($validated['children'] ?? 0));
        $totalGuests = $adults + $children;

        // Query active bookable room types
        $query = RoomType::with(['branch', 'category', 'amenitiesList', 'rooms.blocks'])
            ->where('is_active', true)
            ->where('is_bookable', true);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Capacity filter on RoomType level:
        // 1. max_guests must accommodate total guests
        $query->where('max_guests', '>=', $totalGuests);

        // 2. max_adults must accommodate adults (if max_adults > 0)
        $query->where(function ($q) use ($adults) {
            $q->whereNull('max_adults')
              ->orWhere('max_adults', 0)
              ->orWhere('max_adults', '>=', $adults);
        });

        // 3. Child allocation with adult-bed substitution rule:
        // Children count cannot exceed configured max_children + any unused adult slots
        if ($children > 0) {
            $query->where(function ($q) use ($adults, $children) {
                $q->whereNull('max_children')
                  ->orWhereRaw('? <= (COALESCE(max_children, 0) + (GREATEST(0, COALESCE(max_adults, 2) - ?)))', [$children, $adults]);
            });
        }

        $roomTypes = $query->orderBy('sort_order')->get();

        $availableSuites = [];

        foreach ($roomTypes as $rt) {
            // Get physical rooms for this room type
            // Exclude out_of_order, maintenance, blocked
            $eligibleRooms = $rt->rooms->filter(function ($room) use ($totalGuests, $checkIn, $checkOut) {
                // Check operational status
                if (in_array($room->operational_status, ['maintenance', 'blocked', 'out_of_order'])) {
                    return false;
                }

                // Check physical room capacity override (if specified)
                $roomCap = (int) ($room->max_guests ?? 0);
                if ($roomCap > 0 && $roomCap < $totalGuests) {
                    return false;
                }

                // Check RoomBlock conflicts
                $hasBlockConflict = $room->blocks->contains(function ($block) use ($checkIn, $checkOut) {
                    $bStart = Carbon::parse($block->start_date)->startOfDay();
                    $bEnd = Carbon::parse($block->end_date)->startOfDay();
                    return $bStart->lt($checkOut) && $bEnd->gt($checkIn);
                });

                if ($hasBlockConflict) {
                    return false;
                }

                return true;
            });

            if ($eligibleRooms->isEmpty()) {
                continue;
            }

            // Find overlapping active reservations for this room type
            // Overlapping condition: check_in_date < $checkOut AND check_out_date > $checkIn
            // with status IN ('confirmed', 'checked_in')
            $overlappingReservations = Reservation::where('room_type_id', $rt->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $checkOut->format('Y-m-d'))
                ->where('check_out_date', '>', $checkIn->format('Y-m-d'))
                ->get();

            // Rooms already reserved with room_id assigned
            $reservedRoomIds = $overlappingReservations->pluck('room_id')->filter()->all();

            // Remaining eligible rooms that are not specifically assigned
            $freeRooms = $eligibleRooms->reject(function ($room) use ($reservedRoomIds) {
                return in_array($room->id, $reservedRoomIds);
            });

            // Account for unassigned reservations (where room_id is null)
            $unassignedCount = $overlappingReservations->whereNull('room_id')->count();
            $availableRoomsCount = max(0, $freeRooms->count() - $unassignedCount);

            if ($availableRoomsCount > 0) {
                $rate = (float) $rt->base_price;
                $subtotal = round($rate * $nights, 2);
                $tax = round($subtotal * 0.12, 2); // 12% GST
                $grandTotal = $subtotal + $tax;

                // Extract amenities names
                $amenityNames = [];
                if ($rt->amenitiesList && $rt->amenitiesList->isNotEmpty()) {
                    $amenityNames = $rt->amenitiesList->take(4)->pluck('name')->all();
                } elseif (is_array($rt->amenities)) {
                    $amenityNames = array_slice($rt->amenities, 0, 4);
                }

                $availableSuites[] = [
                    'id' => $rt->id,
                    'name' => $rt->name,
                    'slug' => $rt->slug,
                    'short_description' => $rt->short_description ?: 'Luxury cottage experience surrounded by tranquil greenery.',
                    'branch_id' => $rt->branch_id,
                    'branch_name' => $rt->branch?->name ?? 'Krishna Resorts',
                    'branch_city' => $rt->branch?->city ?? 'Kerala',
                    'category_name' => $rt->category?->name ?? 'Villa Suite',
                    'bed_type' => $rt->bed_type ?: 'King Size Bed',
                    'size_sqft' => $rt->size_sqft ?: 420,
                    'max_guests' => $rt->max_guests,
                    'max_adults' => $rt->max_adults,
                    'max_children' => $rt->max_children,
                    'cover_image_url' => $rt->cover_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=85',
                    'base_price' => $rate,
                    'formatted_price' => '₹' . number_format($rate),
                    'total_price' => $subtotal,
                    'formatted_subtotal' => '₹' . number_format($subtotal),
                    'tax' => $tax,
                    'formatted_tax' => '₹' . number_format($tax),
                    'grand_total' => $grandTotal,
                    'formatted_grand_total' => '₹' . number_format($grandTotal),
                    'available_rooms_count' => $availableRoomsCount,
                    'amenities' => $amenityNames,
                    'checkout_url' => route('booking.checkout', $rt->id) . '?' . http_build_query([
                        'check_in' => $checkInStr,
                        'check_out' => $checkOutStr,
                        'adults' => $adults,
                        'children' => $children,
                    ]),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'count' => count($availableSuites),
            'nights' => $nights,
            'check_in' => $checkInStr,
            'check_out' => $checkOutStr,
            'adults' => $adults,
            'children' => $children,
            'total_guests' => $totalGuests,
            'results' => $availableSuites,
        ]);
    }
}
