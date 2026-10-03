<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Guest;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingAvailabilityService
{
    /**
     * Evaluate raw adult and child counts with optional child ages against resort property rules.
     * Reclassifies children exceeding child_max_age as effective adults.
     */
    public function evaluatePartyComposition(int $rawAdults, int $rawChildren, array $childAges = []): array
    {
        $rawAdults = max(1, $rawAdults);
        $rawChildren = max(0, $rawChildren);
        $childMaxAge = (int) Setting::get('child_max_age', 12);
        $infantMaxAge = (int) Setting::get('infant_max_age', 5);

        $reclassifiedAdults = 0;
        $infantCount = 0;
        $childCount = 0;

        if (!empty($childAges)) {
            foreach ($childAges as $age) {
                $a = (int) $age;
                if ($a > $childMaxAge) {
                    $reclassifiedAdults++;
                } elseif ($a <= $infantMaxAge) {
                    $infantCount++;
                } else {
                    $childCount++;
                }
            }
        }

        $effectiveAdults = $rawAdults + $reclassifiedAdults;
        $effectiveChildren = max(0, $rawChildren - $reclassifiedAdults);
        $totalGuests = $effectiveAdults + $effectiveChildren;

        return [
            'raw_adults' => $rawAdults,
            'raw_children' => $rawChildren,
            'effective_adults' => $effectiveAdults,
            'effective_children' => $effectiveChildren,
            'reclassified_adults' => $reclassifiedAdults,
            'infant_count' => $infantCount,
            'total_guests' => $totalGuests,
            'child_max_age' => $childMaxAge,
            'infant_max_age' => $infantMaxAge,
        ];
    }

    /**
     * Validate party composition against room type capacity rules.
     * Enforces max_guests, max_adults, and child allocation with adult-bed substitution.
     */
    public function validateRoomTypeCapacity(RoomType $roomType, int $adults, int $children, int $roomsCount = 1): array
    {
        $roomsCount = max(1, $roomsCount);
        $totalGuests = $adults + $children;

        $maxGuestsTotal = (int) ($roomType->max_guests * $roomsCount);
        $maxAdultsTotal = $roomType->max_adults ? (int) ($roomType->max_adults * $roomsCount) : null;
        $maxChildrenConfig = (int) (($roomType->max_children ?? 1) * $roomsCount);

        // 1. Total guests capacity
        if ($totalGuests > $maxGuestsTotal) {
            return [
                'valid' => false,
                'message' => "The selected suite ({$roomType->name}) accommodates a maximum of {$maxGuestsTotal} total guests across {$roomsCount} room(s). Requested: {$totalGuests} guests.",
            ];
        }

        // 2. Adult limit
        if ($maxAdultsTotal && $adults > $maxAdultsTotal) {
            return [
                'valid' => false,
                'message' => "The selected suite accommodates a maximum of {$maxAdultsTotal} adult(s) across {$roomsCount} room(s). Requested: {$adults} adults.",
            ];
        }

        // 3. Child bed substitution: children can use unused adult slots
        $unusedAdultSlots = $maxAdultsTotal ? max(0, $maxAdultsTotal - $adults) : 0;
        $maxAllowedChildren = $maxChildrenConfig + $unusedAdultSlots;

        if ($children > 0 && $children > $maxAllowedChildren) {
            return [
                'valid' => false,
                'message' => "The selected suite cannot accommodate {$children} child(ren) with {$adults} adult(s). Maximum allowable children with bed substitution is {$maxAllowedChildren}.",
            ];
        }

        return [
            'valid' => true,
            'message' => null,
        ];
    }

    /**
     * Calculate true stay pricing with per-night rates, weekend pricing, 12% GST, and 20% deposit.
     */
    public function calculateStayPricing(RoomType $roomType, Carbon $checkIn, Carbon $checkOut, int $roomsCount = 1, string $paymentChoice = 'full'): array
    {
        $roomsCount = max(1, $roomsCount);
        $nights = max(1, $checkIn->diffInDays($checkOut));
        
        $basePrice = (float) $roomType->base_price;
        $weekendPrice = (float) ($roomType->weekend_price ?: $basePrice);

        $nightlyBreakdown = [];
        $subtotalPerRoom = 0.0;
        $weekendNights = 0;
        $weekdayNights = 0;

        $current = $checkIn->copy()->startOfDay();
        $end = $checkOut->copy()->startOfDay();

        while ($current->lt($end)) {
            // Friday and Saturday nights are standard resort weekend peak nights
            $isWeekend = ($current->isFriday() || $current->isSaturday());
            $rate = $isWeekend ? $weekendPrice : $basePrice;

            if ($isWeekend) {
                $weekendNights++;
            } else {
                $weekdayNights++;
            }

            $nightlyBreakdown[] = [
                'date' => $current->format('Y-m-d'),
                'day_name' => $current->format('D'),
                'is_weekend' => $isWeekend,
                'rate' => $rate,
            ];

            $subtotalPerRoom += $rate;
            $current->addDay();
        }

        $subtotal = round($subtotalPerRoom * $roomsCount, 2);
        $averageNightlyRate = round($subtotalPerRoom / $nights, 2);

        // Fetch dynamic room GST rate configured by admin in Common Settings (default: 12.0%)
        $gstRate = (float) Setting::get('tax_gst_rate', Setting::get('gst_room_standard', 12.0));

        // Airbnb-style statutory slab compliance (SAC 996311): if enabled and tariff > ₹7,500/night, auto-apply 18% GST
        $slabModeEnabled = (bool) Setting::get('gst_slab_mode_enabled', false);
        if ($slabModeEnabled && $averageNightlyRate > 7500) {
            $gstRate = 18.0;
        }

        $tax = round($subtotal * ($gstRate / 100), 2);
        $total = round($subtotal + $tax, 2);

        // Advance deposit percentage configured by admin in Common Settings (default: 20%)
        $depositPct = (float) Setting::get('deposit_advance_percentage', 20.0);
        $deposit = round($total * ($depositPct / 100), 2);

        $amountToPayNow = ($paymentChoice === 'deposit_20') ? $deposit : $total;
        $balanceDueAtCheckIn = round($total - $amountToPayNow, 2);

        return [
            'nights' => $nights,
            'rooms_count' => $roomsCount,
            'base_price' => $basePrice,
            'weekend_price' => $weekendPrice,
            'average_nightly_rate' => $averageNightlyRate,
            'weekday_nights' => $weekdayNights,
            'weekend_nights' => $weekendNights,
            'nightly_breakdown' => $nightlyBreakdown,
            'subtotal' => $subtotal,
            'tax_rate' => $gstRate,
            'tax' => $tax,
            'total' => $total,
            'deposit_percentage' => $depositPct,
            'deposit' => $deposit,
            'amount_to_pay_now' => $amountToPayNow,
            'balance_due_at_checkin' => $balanceDueAtCheckIn,
            'payment_choice' => $paymentChoice,
        ];
    }

    /**
     * Get truly available physical rooms for a given room type and date range [checkIn, checkOut).
     * Strictly excludes:
     * 1. Operational status IN ('maintenance', 'blocked', 'out_of_order')
     * 2. Active overlapping RoomBlocks (maintenance, renovation, admin hold)
     * 3. Active overlapping Confirmed or Checked-In Reservations
     * 4. Active overlapping Temporary Holds (status = 'hold' and hold_expires_at > now())
     * 5. Unassigned Reservations (where room_id is NULL)
     */
    public function getAvailablePhysicalRooms(int $roomTypeId, string $checkInStr, string $checkOutStr, ?int $excludeReservationId = null, bool $forUpdate = false): Collection
    {
        $ci = Carbon::parse($checkInStr)->toDateString();
        $co = Carbon::parse($checkOutStr)->toDateString();
        $now = Carbon::now();

        // Release expired holds first
        $this->releaseExpiredHolds();

        $query = Room::where('room_type_id', $roomTypeId)
            ->whereNotIn('operational_status', ['maintenance', 'blocked', 'out_of_order'])
            // Exclude rooms with overlapping room_blocks
            ->whereDoesntHave('blocks', function ($q) use ($ci, $co) {
                $q->where('start_date', '<', $co)
                  ->where('end_date', '>', $ci);
            })
            // Exclude rooms with overlapping confirmed or checked-in reservations
            ->whereDoesntHave('reservations', function ($q) use ($ci, $co, $excludeReservationId) {
                $q->whereIn('status', ['confirmed', 'checked_in'])
                  ->where('check_in_date', '<', $co)
                  ->where('check_out_date', '>', $ci)
                  ->when($excludeReservationId, fn($sq) => $sq->where('id', '!=', $excludeReservationId));
            })
            // Exclude rooms with active unexpired temporary holds
            ->whereDoesntHave('reservations', function ($q) use ($ci, $co, $now, $excludeReservationId) {
                $q->where('status', 'hold')
                  ->where('hold_expires_at', '>', $now)
                  ->where('check_in_date', '<', $co)
                  ->where('check_out_date', '>', $ci)
                  ->when($excludeReservationId, fn($sq) => $sq->where('id', '!=', $excludeReservationId));
            });

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        $freeRooms = $query->get();

        // Account for unassigned confirmed reservations (where room_id is NULL)
        $unassignedCount = Reservation::where('room_type_id', $roomTypeId)
            ->whereNull('room_id')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<', $co)
            ->where('check_out_date', '>', $ci)
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->count();

        // Also account for unassigned active holds
        $unassignedHolds = Reservation::where('room_type_id', $roomTypeId)
            ->whereNull('room_id')
            ->where('status', 'hold')
            ->where('hold_expires_at', '>', $now)
            ->where('check_in_date', '<', $co)
            ->where('check_out_date', '>', $ci)
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->count();

        $totalUnassigned = $unassignedCount + $unassignedHolds;

        if ($totalUnassigned > 0 && $freeRooms->count() > 0) {
            return $freeRooms->slice($totalUnassigned)->values();
        }

        return $freeRooms;
    }

    /**
     * Create a 10-minute temporary inventory hold for the customer during checkout.
     * Prevents other guests from booking the same room while payment is processing.
     */
    public function createTemporaryHold(
        User $user,
        RoomType $roomType,
        string $checkInStr,
        string $checkOutStr,
        int $adults,
        int $children,
        int $roomsCount,
        float $total,
        float $nightlyRate,
        float $subtotal,
        float $tax,
        array $guestData,
        string $paymentChoice,
        ?string $razorpayOrderId = null
    ): array {
        return DB::transaction(function () use (
            $user, $roomType, $checkInStr, $checkOutStr, $adults, $children,
            $roomsCount, $total, $nightlyRate, $subtotal, $tax, $guestData,
            $paymentChoice, $razorpayOrderId
        ) {
            // Find available physical room with row lock
            $availableRooms = $this->getAvailablePhysicalRooms($roomType->id, $checkInStr, $checkOutStr, null, true);

            if ($availableRooms->count() < $roomsCount) {
                return [
                    'success' => false,
                    'message' => "Selected suite ({$roomType->name}) is fully booked or held for these dates. Please choose another date or suite.",
                ];
            }

            $selectedRoom = $availableRooms->first();

            // Link or create guest profile
            $guest = Guest::firstOrCreate(
                ['email' => $guestData['email']],
                [
                    'user_id' => $user->id,
                    'first_name' => $guestData['first_name'],
                    'last_name' => $guestData['last_name'],
                    'phone' => $guestData['phone'],
                    'city' => $guestData['city'] ?? 'Kerala',
                    'country' => $guestData['country'] ?? 'India',
                    'vip_level' => 'standard',
                ]
            );

            $holdDuration = (int) Setting::get('hold_duration_minutes', 10);
            $expiresAt = Carbon::now()->addMinutes($holdDuration);
            $holdCode = 'KR-HOLD-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // Clean up any stale holds for this user on same dates
            Reservation::where('created_by', $user->id)
                ->where('status', 'hold')
                ->where('hold_expires_at', '<=', Carbon::now())
                ->delete();

            $hold = Reservation::create([
                'booking_code' => $holdCode,
                'branch_id' => $roomType->branch_id,
                'room_type_id' => $roomType->id,
                'room_id' => $selectedRoom->id,
                'guest_id' => $guest->id,
                'check_in_date' => $checkInStr,
                'check_out_date' => $checkOutStr,
                'adults' => $adults,
                'children' => $children,
                'rooms_count' => $roomsCount,
                'nightly_rate' => $nightlyRate,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'discount_amount' => 0.00,
                'total_amount' => $total,
                'paid_amount' => 0.00,
                'refunded_amount' => 0.00,
                'status' => 'hold',
                'payment_status' => 'pending',
                'payment_method' => 'online',
                'special_requests' => $guestData['special_requests'] ?? null,
                'internal_notes' => json_encode([
                    'payment_choice' => $paymentChoice,
                    'razorpay_order_id' => $razorpayOrderId,
                    'held_at' => Carbon::now()->toIso8601String(),
                ]),
                'hold_expires_at' => $expiresAt,
                'is_counter_booking' => false,
                'created_by' => $user->id,
            ]);

            return [
                'success' => true,
                'reservation' => $hold,
                'room' => $selectedRoom,
                'expires_at' => $expiresAt,
                'hold_duration_minutes' => $holdDuration,
            ];
        });
    }

    /**
     * Transition a held reservation to Confirmed upon verified payment.
     */
    public function confirmHeldReservation(
        Reservation $reservation,
        string $paymentId,
        string $orderId,
        ?string $signature,
        string $paymentMethod,
        string $paymentChoice,
        float $paidAmount,
        User $user
    ): Reservation {
        return DB::transaction(function () use (
            $reservation, $paymentId, $orderId, $signature, $paymentMethod,
            $paymentChoice, $paidAmount, $user
        ) {
            $bookingCode = 'KR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $paymentStatus = ($paymentChoice === 'deposit_20') ? 'partial' : 'paid';

            $reservation->update([
                'booking_code' => $bookingCode,
                'status' => 'confirmed',
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'paid_amount' => $paidAmount,
                'hold_expires_at' => null,
                'internal_notes' => 'Confirmed online via Razorpay (' . strtoupper($paymentMethod) . ')',
            ]);

            // If check-in is today, set physical room to reserved
            if (Carbon::parse($reservation->check_in_date)->isToday() && $reservation->room) {
                if ($reservation->room->operational_status === 'available') {
                    $reservation->room->update(['operational_status' => 'reserved']);
                }
            }

            // Guest metrics
            if ($reservation->guest) {
                $reservation->guest->increment('total_stays', 1);
                $reservation->guest->increment('total_spent', $reservation->total_amount);
            }

            // Status transition log
            ReservationStatusLog::create([
                'reservation_id' => $reservation->id,
                'from_status' => 'hold',
                'to_status' => 'confirmed',
                'user_id' => $user->id,
                'note' => "Held inventory successfully converted to confirmed booking #{$bookingCode} (" . strtoupper($paymentMethod) . ")",
                'created_at' => Carbon::now(),
            ]);

            // Financial ledger record
            Payment::create([
                'payable_type' => Reservation::class,
                'payable_id' => $reservation->id,
                'branch_id' => $reservation->branch_id,
                'transaction_id' => $paymentId,
                'amount' => $paidAmount,
                'payment_method' => $paymentMethod,
                'gateway' => 'razorpay',
                'status' => 'successful',
                'gateway_response' => [
                    'razorpay_order_id' => $orderId,
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $signature,
                    'payment_choice' => $paymentChoice,
                ],
                'notes' => "Customer booking settlement for #{$bookingCode} via " . strtoupper($paymentMethod) . ($paymentChoice === 'deposit_20' ? " (20% Deposit ₹{$paidAmount}, Balance ₹" . number_format($reservation->total_amount - $paidAmount, 2) . " due at check-in)" : " (Full payment ₹{$reservation->total_amount})"),
                'created_by' => $user->id,
            ]);

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => ($reservation->guest ? $reservation->guest->full_name : 'Guest') . ' (Online Guest)',
                'role' => 'customer',
                'branch_id' => $reservation->branch_id,
                'action' => 'confirm_reservation_hold',
                'entity_type' => 'Reservation',
                'entity_id' => $reservation->id,
                'previous_values' => ['status' => 'hold'],
                'new_values' => [
                    'booking_code' => $bookingCode,
                    'status' => 'confirmed',
                    'total' => $reservation->total_amount,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $paymentStatus,
                ],
                'ip_address' => request()->ip(),
                'created_at' => Carbon::now(),
            ]);

            // SMS Notification
            if ($reservation->guest && $reservation->guest->phone) {
                $branchName = $reservation->branch ? $reservation->branch->name : 'Country Side Cottages';
                $branchAddress = $reservation->branch ? $reservation->branch->full_address : 'Kerala, India';
                $paymentNote = ($paymentChoice === 'deposit_20') 
                    ? "20% Deposit of ₹" . number_format($paidAmount, 2) . " paid. Balance ₹" . number_format($reservation->total_amount - $paidAmount, 2) . " due at check-in." 
                    : "Paid in full (₹" . number_format($reservation->total_amount, 2) . ").";

                NotificationLog::create([
                    'event' => 'reservation_confirmed',
                    'channel' => 'sms',
                    'recipient' => $reservation->guest->phone,
                    'branch_id' => $reservation->branch_id,
                    'reference_type' => Reservation::class,
                    'reference_id' => $reservation->id,
                    'subject' => "Booking Confirmed #{$bookingCode} - Country Side Cottages",
                    'message_body' => "Dear {$reservation->guest->first_name}, your stay at {$branchName} is confirmed! Ref: #{$bookingCode}. Dates: {$reservation->check_in_date} to {$reservation->check_out_date}. {$paymentNote} Resort Location: {$branchAddress}. - Country Side Cottages",
                    'status' => 'delivered',
                    'sent_at' => Carbon::now(),
                ]);
            }

            // Dispatch Email and Telegram notifications
            try {
                app(EmailNotificationService::class)->sendBookingAlert($reservation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Email Booking Alert failed: " . $e->getMessage());
            }

            try {
                app(TelegramNotificationService::class)->sendBookingAlert($reservation);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Telegram Booking Alert failed: " . $e->getMessage());
            }

            return $reservation;
        });
    }

    /**
     * Locate an active temporary hold by its Razorpay Order ID.
     */
    public function findHoldByRazorpayOrderId(string $orderId): ?Reservation
    {
        return Reservation::where('status', 'hold')
            ->where(function ($q) use ($orderId) {
                $q->where('internal_notes->razorpay_order_id', $orderId)
                  ->orWhere('internal_notes', 'like', '%"razorpay_order_id":"' . $orderId . '"%')
                  ->orWhere('internal_notes', 'like', '%\"razorpay_order_id\":\"' . $orderId . '\"%');
            })
            ->first();
    }

    /**
     * Automatically release temporary holds that exceeded their expiration timestamp.
     */
    public function releaseExpiredHolds(): int
    {
        $expiredHolds = Reservation::where('status', 'hold')
            ->where('hold_expires_at', '<=', Carbon::now())
            ->get();

        $count = 0;
        foreach ($expiredHolds as $hold) {
            $hold->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'cancellation_reason' => 'Temporary booking hold expired without payment confirmation.',
            ]);

            ReservationStatusLog::create([
                'reservation_id' => $hold->id,
                'from_status' => 'hold',
                'to_status' => 'cancelled',
                'note' => 'System automatically released 10-minute temporary inventory hold due to expiration.',
                'created_at' => Carbon::now(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Safely update physical room operational status on cancellation without
     * overwriting another guest's active in-house stay today.
     */
    public function safeReleaseRoomOperationalStatus(Reservation $reservation): void
    {
        if (!$reservation->room) {
            return;
        }

        $room = $reservation->room;
        $today = Carbon::today();
        $ci = Carbon::parse($reservation->check_in_date)->startOfDay();
        $co = Carbon::parse($reservation->check_out_date)->startOfDay();

        // Only modify room operational status if the cancelled reservation applies to today
        if ($today->betweenIncluded($ci, $co)) {
            // Check if there is another confirmed or checked-in reservation for today
            $hasOtherActiveToday = Reservation::where('room_id', $room->id)
                ->where('id', '!=', $reservation->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<=', $today->toDateString())
                ->where('check_out_date', '>', $today->toDateString())
                ->exists();

            if (!$hasOtherActiveToday && in_array($room->operational_status, ['reserved', 'occupied'])) {
                $room->update(['operational_status' => 'available']);
            }
        }
    }
}
