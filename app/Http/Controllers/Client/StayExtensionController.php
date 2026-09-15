<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\Guest;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Models\Room;
use App\Models\StayExtensionRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StayExtensionController extends Controller
{
    /**
     * Check current room availability & run Smart Room Re-Allocation algorithm.
     */
    public function checkExtensionAvailability(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'new_checkout_date' => 'required|date',
        ]);

        $reservation = Reservation::with(['room.roomType', 'roomType', 'branch'])->findOrFail($id);

        $currentCheckout = Carbon::parse($reservation->check_out_date);
        $newCheckout = Carbon::parse($validated['new_checkout_date']);

        if (!$newCheckout->isAfter($currentCheckout)) {
            return response()->json([
                'success' => false,
                'message' => 'New check-out date must be after your current departure date (' . $currentCheckout->format('M d, Y') . ').',
            ], 422);
        }

        $extraNights = max(1, $currentCheckout->diffInDays($newCheckout));
        $totalGuests = max(1, (int)$reservation->adults + (int)$reservation->children);
        $ci = $currentCheckout->toDateString();
        $co = $newCheckout->toDateString();

        // 1. Check if CURRENT assigned room is free for extended dates
        $sameRoomAvailable = false;
        $currentRoomInfo = null;

        if ($reservation->room_id) {
            $isOccupied = Reservation::where('room_id', $reservation->room_id)
                ->where('id', '!=', $reservation->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $co)
                ->where('check_out_date', '>', $ci)
                ->exists();

            if (!$isOccupied && !in_array($reservation->room->operational_status, ['maintenance', 'blocked', 'out_of_order'])) {
                $sameRoomAvailable = true;
                $rate = (float) $reservation->nightly_rate;
                $subtotal = $rate * $extraNights;
                $tax = round($subtotal * 0.12, 2);
                $standardTotal = $subtotal + $tax;

                $currentRoomInfo = [
                    'room_id' => $reservation->room->id,
                    'room_number' => $reservation->room->room_number,
                    'floor' => $reservation->room->floor,
                    'room_type_name' => $reservation->roomType ? $reservation->roomType->name : 'Current Cottage',
                    'extra_nights' => $extraNights,
                    'nightly_rate' => $rate,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total_amount' => $standardTotal,
                ];
            }
        }

        // 2. SMART RECOMMENDATION ALGORITHM:
        // Query ALL physical rooms in this branch that are free for [ci, co)
        $freeRooms = Room::with('roomType')
            ->where('branch_id', $reservation->branch_id)
            ->whereNotIn('operational_status', ['maintenance', 'blocked', 'out_of_order'])
            ->get()
            ->filter(function ($room) use ($ci, $co, $reservation) {
                return Reservation::where('room_id', $room->id)
                    ->where('id', '!=', $reservation->id)
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->where('check_in_date', '<', $co)
                    ->where('check_out_date', '>', $ci)
                    ->doesntExist();
            })
            ->values();

        // Group A: Single Room Upgrades / Alternates satisfying total guest party
        $singleRooms = $freeRooms->filter(function ($r) use ($totalGuests) {
            $cap = $r->roomType ? (int) $r->roomType->max_guests : 2;
            return $cap >= $totalGuests;
        })->map(function ($r) use ($extraNights) {
            $rate = (float) ($r->roomType ? $r->roomType->base_price : 3000);
            $sub = $rate * $extraNights;
            $tax = round($sub * 0.12, 2);
            return [
                'room_ids' => [$r->id],
                'display_label' => "Villa {$r->room_number} (" . ($r->roomType ? $r->roomType->name : 'Cottage') . ")",
                'room_type' => $r->roomType ? $r->roomType->name : 'Cottage',
                'capacity' => $r->roomType ? $r->roomType->max_guests : 2,
                'cover_image' => $r->roomType ? $r->roomType->cover_image_url : null,
                'rate_per_night' => $rate,
                'total_amount' => $sub + $tax,
                'type' => 'single_room',
            ];
        })->values();

        // Group B: Multi-Room Combinations if single rooms limited or for flexible families
        $multiRoomCombinations = collect();
        if ($singleRooms->isEmpty() || $totalGuests > 2) {
            // Check pairs of rooms
            for ($i = 0; $i < count($freeRooms); $i++) {
                for ($j = $i + 1; $j < count($freeRooms); $j++) {
                    $r1 = $freeRooms[$i];
                    $r2 = $freeRooms[$j];
                    $combinedCap = ($r1->roomType ? $r1->roomType->max_guests : 2) + ($r2->roomType ? $r2->roomType->max_guests : 2);
                    if ($combinedCap >= $totalGuests) {
                        $rate1 = (float) ($r1->roomType ? $r1->roomType->base_price : 3000);
                        $rate2 = (float) ($r2->roomType ? $r2->roomType->base_price : 3000);
                        $combinedSub = ($rate1 + $rate2) * $extraNights;
                        $combinedTax = round($combinedSub * 0.12, 2);

                        $multiRoomCombinations->push([
                            'room_ids' => [$r1->id, $r2->id],
                            'display_label' => "Villa {$r1->room_number} + Villa {$r2->room_number}",
                            'room_type' => "Combined Suite (" . ($r1->roomType ? $r1->roomType->name : 'Unit 1') . " & " . ($r2->roomType ? $r2->roomType->name : 'Unit 2') . ")",
                            'capacity' => $combinedCap,
                            'cover_image' => $r1->roomType ? $r1->roomType->cover_image_url : null,
                            'rate_per_night' => $rate1 + $rate2,
                            'total_amount' => $combinedSub + $combinedTax,
                            'type' => 'multi_room',
                        ]);

                        if ($multiRoomCombinations->count() >= 3) break 2;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'same_room_available' => $sameRoomAvailable,
            'current_room_info' => $currentRoomInfo,
            'extra_nights' => $extraNights,
            'total_guests' => $totalGuests,
            'smart_recommendations' => [
                'single_rooms' => $singleRooms->take(4),
                'multi_room_combinations' => $multiRoomCombinations,
            ],
        ]);
    }

    /**
     * Guest submits extension request for manager discount review.
     */
    public function submitExtensionRequest(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'new_checkout_date' => 'required|date',
            'allocation_type' => 'required|in:same_room,single_room,multi_room',
            'allocated_room_ids' => 'required|array|min:1',
            'standard_amount' => 'required|numeric|min:0',
            'guest_notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $reservation = Reservation::with(['branch', 'guest', 'room'])->findOrFail($id);

        $currentCheckout = Carbon::parse($reservation->check_out_date);
        $newCheckout = Carbon::parse($validated['new_checkout_date']);
        $extraNights = max(1, $currentCheckout->diffInDays($newCheckout));

        // Create extension request record
        $ext = StayExtensionRequest::create([
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'user_id' => $user->id,
            'current_checkout_date' => $currentCheckout->toDateString(),
            'requested_checkout_date' => $newCheckout->toDateString(),
            'extra_nights' => $extraNights,
            'allocation_type' => $validated['allocation_type'],
            'allocated_room_ids' => $validated['allocated_room_ids'],
            'standard_amount' => $validated['standard_amount'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'guest_notes' => $validated['guest_notes'] ?? null,
        ]);

        // Dispatches high-priority enquiry message to branch manager
        $enquiry = Enquiry::firstOrCreate(
            ['reservation_id' => $reservation->id, 'topic' => 'booking_related'],
            [
                'ticket_number' => 'EXT-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'user_id' => $user->id,
                'guest_id' => $reservation->guest_id,
                'branch_id' => $reservation->branch_id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'subject' => "Stay Extension Request #{$ext->id} - {$reservation->booking_code}",
                'status' => 'new',
                'priority' => 'urgent',
                'has_unread_messages' => true,
                'last_message_at' => Carbon::now(),
            ]
        );

        EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'customer',
            'user_id' => $user->id,
            'message' => "🛎️ [Stay Extension Request #{$ext->id}] Guest requested +{$extraNights} night(s) until {$newCheckout->format('M d, Y')} (Est. ₹" . number_format($validated['standard_amount'], 2) . "). Notes: " . ($validated['guest_notes'] ?? 'None'),
            'is_internal_note' => false,
        ]);

        // Dispatch real-time CallMeBot WhatsApp alert to branch manager / admin
        try {
            app(\App\Services\WhatsAppNotificationService::class)->sendStayExtensionAlert($ext, $reservation);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("WhatsApp Stay Extension Alert failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Your holiday extension request has been transmitted to the branch manager! You will receive an offer notification shortly.",
            'extension_request' => $ext,
        ]);
    }

    /**
     * Admin/Manager reviews and approves extension with custom discounted rate offer.
     */
    public function offerExtensionDiscount(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'offered_amount' => 'required|numeric|min:0',
            'manager_notes' => 'nullable|string|max:500',
        ]);

        $ext = StayExtensionRequest::with(['reservation.guest', 'reservation.branch'])->findOrFail($id);
        $res = $ext->reservation;

        $offeredAmount = (float) $validated['offered_amount'];
        $standardAmount = (float) $ext->standard_amount;
        $discountPct = $standardAmount > 0 ? max(0, round((($standardAmount - $offeredAmount) / $standardAmount) * 100, 1)) : 0;

        // ATOMIC RESERVATION UPDATE:
        // Manager approval locks in the extension so guest's stay continues uninterrupted.
        DB::transaction(function () use ($ext, $res, $offeredAmount, $discountPct, $validated) {
            $newCheckout = $ext->requested_checkout_date->toDateString();

            // Check if allocated rooms are still free
            $allocatedIds = $ext->allocated_room_ids ?? [];
            foreach ($allocatedIds as $roomId) {
                $overlap = Reservation::where('room_id', $roomId)
                    ->where('id', '!=', $res->id)
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->where('check_in_date', '<', $newCheckout)
                    ->where('check_out_date', '>', $res->check_out_date->toDateString())
                    ->exists();

                if ($overlap) {
                    throw new \Exception("Cannot approve: Physical room #{$roomId} has a conflicting reservation.");
                }
            }

            // Update StayExtensionRequest
            $ext->update([
                'offered_amount' => $offeredAmount,
                'manager_discount_percentage' => $discountPct,
                'manager_notes' => $validated['manager_notes'] ?? null,
                'status' => 'approved',
                'payment_status' => 'pending',
                'reviewed_by' => auth()->id() ?? 1,
                'reviewed_at' => Carbon::now(),
                'approved_at' => Carbon::now(),
            ]);

            // Extend checkout date on reservation & adjust total balance
            $previousCheckout = $res->check_out_date->toDateString();
            $newTotal = (float) $res->total_amount + $offeredAmount;

            $updatePayload = [
                'check_out_date' => $newCheckout,
                'total_amount' => $newTotal,
                'payment_status' => ($res->paid_amount >= $newTotal) ? 'paid' : 'partial',
            ];

            // If new single room allocated, update room_id
            if (!empty($allocatedIds) && count($allocatedIds) === 1) {
                $updatePayload['room_id'] = $allocatedIds[0];
            }

            $res->update($updatePayload);

            // Log status change
            ReservationStatusLog::create([
                'reservation_id' => $res->id,
                'from_status' => $res->status,
                'to_status' => $res->status,
                'user_id' => auth()->id() ?? 1,
                'note' => "Manager approved stay extension from {$previousCheckout} to {$newCheckout}. Added ₹{$offeredAmount} to folio (Pending payment).",
                'created_at' => Carbon::now(),
            ]);

            // Dispatch SMS notification to guest
            if ($res->guest && $res->guest->phone) {
                NotificationLog::create([
                    'event' => 'stay_extension_approved_offer',
                    'channel' => 'sms',
                    'recipient' => $res->guest->phone,
                    'branch_id' => $res->branch_id,
                    'reference_type' => 'StayExtensionRequest',
                    'reference_id' => $ext->id,
                    'subject' => 'Stay Extension Approved with Special Offer - Krishna Resorts',
                    'message_body' => "Namaste {$res->guest->first_name}! Your stay extension until {$ext->requested_checkout_date->format('M d, Y')} is APPROVED at special rate ₹" . number_format($offeredAmount, 2) . " (Save {$discountPct}%!). Payment can be paid on-hand to cottage staff or at checkout. - Krishna Resorts",
                    'status' => 'delivered',
                    'sent_at' => Carbon::now(),
                ]);
            }

            // Audit log
            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'user_name' => auth()->user()->name ?? 'Branch Manager',
                'role' => auth()->user()->role ?? 'super_admin',
                'branch_id' => $res->branch_id,
                'action' => 'approve_stay_extension',
                'entity_type' => 'StayExtensionRequest',
                'entity_id' => $ext->id,
                'previous_values' => ['checkout' => $previousCheckout, 'standard_amount' => $ext->standard_amount],
                'new_values' => ['checkout' => $newCheckout, 'offered_amount' => $offeredAmount, 'discount_pct' => $discountPct],
                'ip_address' => request()->ip(),
                'created_at' => Carbon::now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Stay extension approved and special offer of ₹" . number_format($offeredAmount, 2) . " transmitted to guest!",
            'extension_request' => $ext->fresh(),
        ]);
    }

    /**
     * Front desk staff / manager records on-hand payment (cash, pos card, upi) even hours or days later.
     */
    public function recordExtensionPayment(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,pos_card,counter_upi,bank_transfer,online',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $ext = StayExtensionRequest::with('reservation')->findOrFail($id);
        $res = $ext->reservation;

        $amountToPay = (float) ($validated['paid_amount'] ?? $ext->offered_amount ?? $ext->standard_amount);

        DB::transaction(function () use ($ext, $res, $validated, $amountToPay) {
            $ext->update([
                'payment_status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $amountToPay,
                'paid_at' => Carbon::now(),
                'status' => 'completed',
            ]);

            $newPaid = (float) $res->paid_amount + $amountToPay;
            $newPaymentStatus = ($newPaid >= (float) $res->total_amount) ? 'paid' : 'partial';

            $res->update([
                'paid_amount' => $newPaid,
                'payment_status' => $newPaymentStatus,
            ]);

            // Create ledger entry in payments
            Payment::create([
                'payable_type' => Reservation::class,
                'payable_id' => $res->id,
                'branch_id' => $res->branch_id,
                'transaction_id' => 'EXT-' . strtoupper(Str::random(8)),
                'amount' => $amountToPay,
                'payment_method' => $validated['payment_method'],
                'gateway' => 'on_hand_counter',
                'status' => 'successful',
                'notes' => 'On-hand/checkout settlement for stay extension #' . $ext->id . ' (' . ($validated['notes'] ?? 'Settled with front desk') . ')',
                'created_by' => auth()->id() ?? 1,
            ]);

            AuditLog::create([
                'user_id' => auth()->id() ?? 1,
                'user_name' => auth()->user()->name ?? 'Front Desk Staff',
                'role' => auth()->user()->role ?? 'super_admin',
                'branch_id' => $res->branch_id,
                'action' => 'record_extension_on_hand_payment',
                'entity_type' => 'StayExtensionRequest',
                'entity_id' => $ext->id,
                'previous_values' => ['payment_status' => 'pending'],
                'new_values' => ['payment_status' => 'paid', 'amount' => $amountToPay, 'method' => $validated['payment_method']],
                'ip_address' => request()->ip(),
                'created_at' => Carbon::now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Extension payment of ₹" . number_format($amountToPay, 2) . " successfully recorded ({$validated['payment_method']})!",
            'extension_request' => $ext->fresh(),
        ]);
    }

    /**
     * Guest settles extension payment online directly from dashboard.
     */
    public function clientPayOnline(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:upi,card,netbanking',
        ]);

        $user = Auth::user();
        $ext = StayExtensionRequest::with('reservation')->findOrFail($id);

        if ($ext->user_id && $ext->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $amount = (float) ($ext->offered_amount ?? $ext->standard_amount);

        DB::transaction(function () use ($ext, $validated, $amount, $user) {
            $ext->update([
                'payment_status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $amount,
                'paid_at' => Carbon::now(),
                'status' => 'completed',
            ]);

            $res = $ext->reservation;
            $newPaid = (float) $res->paid_amount + $amount;
            $res->update([
                'paid_amount' => $newPaid,
                'payment_status' => ($newPaid >= (float) $res->total_amount) ? 'paid' : 'partial',
            ]);

            Payment::create([
                'payable_type' => Reservation::class,
                'payable_id' => $res->id,
                'branch_id' => $res->branch_id,
                'transaction_id' => 'EXT-ONL-' . strtoupper(Str::random(8)),
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'gateway' => 'online_portal',
                'status' => 'successful',
                'notes' => 'Customer online payment for approved stay extension #' . $ext->id,
                'created_by' => $user->id,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Extension payment of ₹' . number_format($amount, 2) . ' confirmed! Enjoy your extended holiday.',
            'extension_request' => $ext->fresh(),
        ]);
    }
}
