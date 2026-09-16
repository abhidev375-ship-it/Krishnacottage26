<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CancellationRule;
use App\Models\Guest;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationActionController extends Controller
{
    /**
     * Dynamically retrieve available physical rooms for specific dates and branch.
     * Prevents front-desk double allocations in real-time.
     */
    public function getAvailableRoomsForDates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type_id' => 'nullable|exists:room_types,id',
        ]);

        $ci = Carbon::parse($validated['check_in_date'])->toDateString();
        $co = Carbon::parse($validated['check_out_date'])->toDateString();

        $roomsQuery = Room::with('roomType')
            ->where('branch_id', $validated['branch_id'])
            ->whereNotIn('operational_status', ['maintenance', 'blocked', 'out_of_order']);

        if (!empty($validated['room_type_id'])) {
            $roomsQuery->where('room_type_id', $validated['room_type_id']);
        }

        $allRooms = $roomsQuery->get();

        $availableRooms = $allRooms->filter(function ($room) use ($ci, $co) {
            return Reservation::where('room_id', $room->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $co)
                ->where('check_out_date', '>', $ci)
                ->doesntExist();
        })->values();

        return response()->json([
            'success' => true,
            'count' => $availableRooms->count(),
            'rooms' => $availableRooms->map(function ($r) {
                return [
                    'id' => $r->id,
                    'room_number' => $r->room_number,
                    'floor' => $r->floor,
                    'room_type_id' => $r->room_type_id,
                    'room_type_name' => $r->roomType ? $r->roomType->name : 'Standard',
                    'base_price' => $r->roomType ? (float) $r->roomType->base_price : 0,
                    'operational_status' => $r->operational_status,
                    'housekeeping_status' => $r->housekeeping_status,
                ];
            }),
        ]);
    }

    public function checkIn($id): JsonResponse
    {
        $reservation = Reservation::with('room')->findOrFail($id);
        
        $previousStatus = $reservation->status;
        $reservation->update([
            'status' => 'checked_in',
            'checked_in_at' => Carbon::now(),
        ]);

        if ($reservation->room) {
            $reservation->room->update(['operational_status' => 'occupied']);
        }

        ReservationStatusLog::create([
            'reservation_id' => $reservation->id,
            'from_status' => $previousStatus,
            'to_status' => 'checked_in',
            'user_id' => auth()->id() ?? 1,
            'note' => 'Guest checked in at front desk.',
            'created_at' => Carbon::now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $reservation->branch_id,
            'action' => 'check_in',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'previous_values' => ['status' => $previousStatus],
            'new_values' => ['status' => 'checked_in'],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Reservation {$reservation->booking_code} checked in successfully.",
            'reservation' => $reservation->load(['room', 'guest', 'roomType']),
        ]);
    }

    public function checkOut($id): JsonResponse
    {
        $reservation = Reservation::with('room')->findOrFail($id);

        $previousStatus = $reservation->status;
        $reservation->update([
            'status' => 'checked_out',
            'checked_out_at' => Carbon::now(),
        ]);

        if ($reservation->room) {
            $reservation->room->update([
                'operational_status' => 'available',
                'housekeeping_status' => 'dirty',
            ]);
        }

        ReservationStatusLog::create([
            'reservation_id' => $reservation->id,
            'from_status' => $previousStatus,
            'to_status' => 'checked_out',
            'user_id' => auth()->id() ?? 1,
            'note' => 'Guest checked out. Room marked dirty for housekeeping.',
            'created_at' => Carbon::now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $reservation->branch_id,
            'action' => 'check_out',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'previous_values' => ['status' => $previousStatus],
            'new_values' => ['status' => 'checked_out'],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Reservation {$reservation->booking_code} checked out. Room marked dirty.",
            'reservation' => $reservation->load(['room', 'guest', 'roomType']),
        ]);
    }

    /**
     * Preview cancellation refund calculation without committing.
     */
    public function previewCancellation($id): JsonResponse
    {
        $reservation = Reservation::with(['branch', 'roomType', 'guest'])->findOrFail($id);

        // Security: Non-admin users can only view their own reservation cancellations
        if (auth()->check() && !in_array(auth()->user()->role ?? '', ['super_admin', 'central_manager', 'branch_manager', 'reservation_staff', 'support_staff'])) {
            $user = auth()->user();
            $guest = $user->guest;
            $isOwner = ($reservation->guest_id && $guest && $reservation->guest_id == $guest->id)
                || ($reservation->created_by == $user->id)
                || ($reservation->guest && $reservation->guest->email == $user->email);
            if (!$isOwner) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to reservation.'], 403);
            }
        }

        $calc = CancellationRule::calculateRefund(
            (float) $reservation->paid_amount,
            $reservation->branch_id,
            $reservation->check_in_date
        );

        return response()->json([
            'success' => true,
            'booking_code' => $reservation->booking_code,
            'total_amount' => (float) $reservation->total_amount,
            'paid_amount' => (float) $reservation->paid_amount,
            'hours_remaining' => $calc['hours_remaining'],
            'refund_percentage' => $calc['percentage'],
            'cashback_amount' => $calc['cashback'],
            'retained_amount' => $calc['retained'],
            'rule_description' => $calc['rule_description'],
        ]);
    }

    /**
     * Cancel reservation with dynamic tiered cashback calculation and automated payback.
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $reservation = Reservation::with(['room', 'guest', 'branch'])->findOrFail($id);

        // Security: Non-admin users can only cancel their own reservations
        if (auth()->check() && !in_array(auth()->user()->role ?? '', ['super_admin', 'central_manager', 'branch_manager', 'reservation_staff', 'support_staff'])) {
            $user = auth()->user();
            $guest = $user->guest;
            $isOwner = ($reservation->guest_id && $guest && $reservation->guest_id == $guest->id)
                || ($reservation->created_by == $user->id)
                || ($reservation->guest && $reservation->guest->email == $user->email);
            if (!$isOwner) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action on reservation.'], 403);
            }
        }

        $reason = $request->input('reason', 'Cancelled by guest request');

        $calc = CancellationRule::calculateRefund(
            (float) $reservation->paid_amount,
            $reservation->branch_id,
            $reservation->check_in_date
        );

        $previousStatus = $reservation->status;
        $cashback = $calc['cashback'];
        $paid = (float) $reservation->paid_amount;

        $newPaymentStatus = 'pending';
        if ($paid > 0) {
            if ($cashback >= $paid) {
                $newPaymentStatus = 'refunded';
            } elseif ($cashback > 0) {
                $newPaymentStatus = 'partial';
            } else {
                $newPaymentStatus = $reservation->payment_status;
            }
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now(),
            'cancellation_reason' => $reason . ' (Rule: ' . $calc['rule_description'] . ')',
            'refunded_amount' => $cashback,
            'payment_status' => $newPaymentStatus,
        ]);

        // Release physical room immediately
        if ($reservation->room) {
            $reservation->room->update(['operational_status' => 'available']);
        }

        // Automated payback ledger record if cashback > 0
        $refundTxnId = null;
        if ($cashback > 0) {
            $refundTxnId = 'REF-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            Payment::create([
                'payable_type' => Reservation::class,
                'payable_id' => $reservation->id,
                'branch_id' => $reservation->branch_id,
                'transaction_id' => $refundTxnId,
                'amount' => $cashback,
                'payment_method' => $reservation->payment_method ?? 'online',
                'gateway' => 'automated_payback_engine',
                'status' => 'refunded',
                'gateway_response' => [
                    'hours_remaining' => $calc['hours_remaining'],
                    'percentage' => $calc['percentage'],
                    'cashback' => $cashback,
                    'rule' => $calc['rule_description'],
                ],
                'notes' => "Automated cancellation payback ({$calc['percentage']}%) for {$reservation->booking_code}",
                'created_by' => auth()->id() ?? 1,
            ]);

            // Dispatch SMS confirmation
            if ($reservation->guest && $reservation->guest->phone) {
                NotificationLog::create([
                    'event' => 'cancellation_automated_cashback',
                    'channel' => 'sms',
                    'recipient' => $reservation->guest->phone,
                    'branch_id' => $reservation->branch_id,
                    'reference_type' => 'Reservation',
                    'reference_id' => $reservation->id,
                    'subject' => "Booking Cancelled & ₹{$cashback} Cashback Initiated - Krishna Cottages",
                    'message_body' => "Dear {$reservation->guest->first_name}, your booking {$reservation->booking_code} has been cancelled. Based on our policy ({$calc['hours_remaining']}h prior to arrival), a cashback of ₹" . number_format($cashback, 2) . " ({$calc['percentage']}%) has been automated (Ref: #{$refundTxnId}). - Krishna Cottages",
                    'status' => 'delivered',
                    'sent_at' => Carbon::now(),
                ]);
            }
        }

        ReservationStatusLog::create([
            'reservation_id' => $reservation->id,
            'from_status' => $previousStatus,
            'to_status' => 'cancelled',
            'user_id' => auth()->id() ?? 1,
            'note' => "Cancelled. Cashback: ₹{$cashback} ({$calc['percentage']}%). Reason: {$reason}",
            'created_at' => Carbon::now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'user_name' => auth()->user()->name ?? 'System Admin',
            'role' => auth()->user()->role ?? 'super_admin',
            'branch_id' => $reservation->branch_id,
            'action' => 'cancellation_cashback_refund',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'previous_values' => ['status' => $previousStatus, 'paid_amount' => $paid],
            'new_values' => [
                'status' => 'cancelled',
                'refunded_amount' => $cashback,
                'refund_percentage' => $calc['percentage'],
                'refund_txn' => $refundTxnId,
            ],
            'ip_address' => request()->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Reservation {$reservation->booking_code} cancelled. Cashback of ₹" . number_format($cashback, 2) . " ({$calc['percentage']}%) automated.",
            'cashback' => $cashback,
            'cashback_amount' => $cashback,
            'percentage' => $calc['percentage'],
            'refund_percentage' => $calc['percentage'],
            'refund_txn' => $refundTxnId,
            'refund_transaction_id' => $refundTxnId,
            'reservation' => $reservation->load(['room', 'guest', 'roomType']),
        ]);
    }

    public function assignRoom(Request $request, $id): JsonResponse
    {
        $reservation = Reservation::findOrFail($id);
        $roomId = $request->input('room_id');

        $room = Room::findOrFail($roomId);
        if ($room->branch_id !== $reservation->branch_id) {
            return response()->json(['success' => false, 'message' => 'Room branch mismatch.'], 422);
        }

        // Concurrency check for the room's dates
        $overlap = Reservation::where('room_id', $room->id)
            ->where('id', '!=', $reservation->id)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<', $reservation->check_out_date)
            ->where('check_out_date', '>', $reservation->check_in_date)
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => "Room {$room->room_number} is already booked by another guest for these dates.",
            ], 422);
        }

        $reservation->update(['room_id' => $room->id]);

        return response()->json([
            'success' => true,
            'message' => "Room {$room->room_number} assigned to reservation {$reservation->booking_code}.",
            'reservation' => $reservation->load(['room', 'guest', 'roomType']),
        ]);
    }

    /**
     * Front-Desk Walk-In Counter Booking with instant physical room locking & software-wide update.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guest_name' => 'required|string|max:150',
            'guest_email' => 'required|email|max:150',
            'guest_phone' => 'required|string|max:30',
            'adults' => 'nullable|integer|min:1|max:10',
            'children' => 'nullable|integer|min:0|max:10',
            'id_proof_type' => 'nullable|string|in:aadhaar,passport,driving_license,voter_id',
            'id_proof_number' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|in:cash,pos_card,counter_upi,bank_transfer',
            'paid_amount' => 'nullable|numeric|min:0',
            'instant_checkin' => 'nullable|boolean',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $nameParts = explode(' ', trim($validated['guest_name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $roomType = RoomType::findOrFail($validated['room_type_id']);
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        $subtotal = $roomType->base_price * $nights;
        $tax = round($subtotal * 0.12, 2);
        $total = $subtotal + $tax;

        $paidAmount = (float) ($validated['paid_amount'] ?? 0);
        $paymentStatus = 'pending';
        if ($paidAmount >= $total) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0) {
            $paymentStatus = 'partial';
        }

        $instantCheckIn = !empty($validated['instant_checkin']);

        // Wrap room locking and creation in atomic transaction
        $reservation = DB::transaction(function () use ($validated, $firstName, $lastName, $roomType, $checkIn, $checkOut, $nights, $subtotal, $tax, $total, $paidAmount, $paymentStatus, $instantCheckIn) {
            $guest = Guest::firstOrCreate(
                ['email' => $validated['guest_email']],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $validated['guest_phone'],
                    'city' => 'Counter Walk-In',
                    'country' => 'India',
                    'vip_level' => 'standard',
                ]
            );

            $guest->increment('total_stays', 1);
            $guest->increment('total_spent', $total);

            // Determine target room with pessimistic lock
            $roomId = $validated['room_id'] ?? null;
            $selectedRoom = null;

            if ($roomId) {
                $selectedRoom = Room::where('id', $roomId)->lockForUpdate()->first();
                if (!$selectedRoom) {
                    throw new \Exception('Specified physical room not found.');
                }
            } else {
                // Auto-pick first free room for this type and dates
                $candidateRooms = Room::where('room_type_id', $roomType->id)
                    ->whereNotIn('operational_status', ['maintenance', 'blocked', 'out_of_order'])
                    ->lockForUpdate()
                    ->get();

                foreach ($candidateRooms as $cand) {
                    $overlap = Reservation::where('room_id', $cand->id)
                        ->whereIn('status', ['confirmed', 'checked_in'])
                        ->where('check_in_date', '<', $checkOut->toDateString())
                        ->where('check_out_date', '>', $checkIn->toDateString())
                        ->exists();

                    if (!$overlap) {
                        $selectedRoom = $cand;
                        break;
                    }
                }
            }

            if (!$selectedRoom) {
                throw new \Exception('No physical rooms of this category are available for the selected dates.');
            }

            // Verify the selected room is strictly free for requested dates
            $isOverlapped = Reservation::where('room_id', $selectedRoom->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $checkOut->toDateString())
                ->where('check_out_date', '>', $checkIn->toDateString())
                ->exists();

            if ($isOverlapped) {
                throw new \Exception("Physical Room {$selectedRoom->room_number} is already booked for these dates.");
            }

            $bookingCode = 'KR-' . date('Y') . '-' . strtoupper(Str::random(5));
            $initialStatus = $instantCheckIn ? 'checked_in' : 'confirmed';

            $res = Reservation::create([
                'booking_code' => $bookingCode,
                'branch_id' => $validated['branch_id'],
                'room_type_id' => $roomType->id,
                'room_id' => $selectedRoom->id,
                'guest_id' => $guest->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => $validated['adults'] ?? 2,
                'children' => $validated['children'] ?? 0,
                'rooms_count' => 1,
                'nightly_rate' => $roomType->base_price,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'refunded_amount' => 0.00,
                'status' => $initialStatus,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'is_counter_booking' => true,
                'counter_booked_by' => auth()->id() ?? 1,
                'id_proof_type' => $validated['id_proof_type'] ?? null,
                'id_proof_number' => $validated['id_proof_number'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
                'checked_in_at' => $instantCheckIn ? Carbon::now() : null,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Instant Software-wide Room Locking
            if ($instantCheckIn) {
                $selectedRoom->update([
                    'operational_status' => 'occupied',
                    'housekeeping_status' => 'clean',
                ]);
            } elseif ($checkIn->isToday() && $selectedRoom->operational_status === 'available') {
                $selectedRoom->update(['operational_status' => 'reserved']);
            }

            // Status Log
            ReservationStatusLog::create([
                'reservation_id' => $res->id,
                'from_status' => null,
                'to_status' => $initialStatus,
                'user_id' => auth()->id() ?? 1,
                'note' => $instantCheckIn 
                    ? 'Walk-in guest registered and checked in immediately at front-desk counter.' 
                    : 'Walk-in reservation confirmed at front-desk counter.',
                'created_at' => Carbon::now(),
            ]);

            // Record counter payment ledger if collected
            if ($paidAmount > 0) {
                Payment::create([
                    'payable_type' => Reservation::class,
                    'payable_id' => $res->id,
                    'branch_id' => $validated['branch_id'],
                    'transaction_id' => 'CTR-' . strtoupper(Str::random(8)),
                    'amount' => $paidAmount,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'gateway' => 'front_desk_counter',
                    'status' => 'successful',
                    'notes' => 'Walk-in counter payment collected at front-desk',
                    'created_by' => auth()->id() ?? 1,
                ]);
            }

            // Immutable Audit Log
            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'user_name' => auth()->user()->name ?? 'Front Desk Staff',
                'role' => auth()->user()->role ?? 'super_admin',
                'branch_id' => $validated['branch_id'],
                'action' => 'counter_walk_in_booking',
                'entity_type' => 'Reservation',
                'entity_id' => $res->id,
                'previous_values' => [],
                'new_values' => [
                    'booking_code' => $bookingCode,
                    'guest_name' => $guest->full_name,
                    'room_number' => $selectedRoom->room_number,
                    'instant_checkin' => $instantCheckIn,
                    'paid_amount' => $paidAmount,
                    'total' => $total,
                ],
                'ip_address' => request()->ip(),
                'created_at' => Carbon::now(),
            ]);

            return $res;
        });

        return response()->json([
            'success' => true,
            'message' => "Counter Walk-In reservation {$reservation->booking_code} registered successfully! Assigned Room: " . ($reservation->room ? $reservation->room->room_number : 'Allocated'),
            'reservation' => $reservation->load(['branch', 'roomType', 'room', 'guest']),
        ]);
    }
}
