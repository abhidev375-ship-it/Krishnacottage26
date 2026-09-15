<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\FolioCharge;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\SpiceOrder;
use App\Services\SmsNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InHouseGuestController extends Controller
{
    /**
     * Retrieve list of all currently checked-in guests with real-time operational badges.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $branchId = $request->query('branch_id');

        $query = Reservation::with(['branch', 'room', 'roomType', 'guest.user', 'folioCharges', 'extensionRequests', 'payments'])
            ->where('status', 'checked_in')
            ->orderBy('check_out_date', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        } elseif ($user && $user->role === 'branch_manager' && $user->branch_access_type === 'specific') {
            $query->whereIn('branch_id', $user->branches->pluck('id'));
        }

        $reservations = $query->get()->map(function ($res) {
            $folioSummary = $res->calculateFolioSummary();
            
            // Count active food orders (new, accepted, preparing, ready)
            $activeFoodOrders = FoodOrder::where('status', '!=', 'completed')
                ->where(function ($q) use ($res) {
                    if ($res->room_id) $q->where('room_id', $res->room_id);
                    if ($res->guest_id) $q->orWhere('guest_id', $res->guest_id);
                })
                ->count();

            // Count pending spice orders
            $activeSpiceOrders = SpiceOrder::whereNotIn('status', ['delivered', 'cancelled'])
                ->where(function ($q) use ($res) {
                    if ($res->guest_id) $q->where('guest_id', $res->guest_id);
                    if ($res->room && $res->room->room_number) $q->orWhere('room_number', $res->room->room_number);
                })
                ->count();

            // Count pending extension requests
            $pendingExtensions = $res->extensionRequests->where('status', 'pending')->count();
            $unsettledExtensions = $res->extensionRequests->where('status', 'approved')->where('payment_status', 'pending')->count();

            // Count active / unread enquiries or service tickets
            $activeTickets = Enquiry::where('reservation_id', $res->id)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count();

            $hasUnreadChat = Enquiry::where('reservation_id', $res->id)
                ->where('has_unread_messages', true)
                ->exists();

            $checkIn = Carbon::parse($res->check_in_date);
            $checkOut = Carbon::parse($res->check_out_date);
            $today = Carbon::today();
            $daysRemaining = max(0, $today->diffInDays($checkOut, false));
            $isDepartingToday = $checkOut->isToday();

            return [
                'id' => $res->id,
                'booking_code' => $res->booking_code,
                'branch_id' => $res->branch_id,
                'branch_name' => $res->branch ? $res->branch->name : 'Resort',
                'room_id' => $res->room_id,
                'room_number' => $res->room ? $res->room->room_number : 'Unassigned',
                'room_type_name' => $res->roomType ? $res->roomType->name : 'Cottage',
                'guest_id' => $res->guest_id,
                'guest_name' => $res->guest ? $res->guest->full_name : 'Guest',
                'guest_phone' => $res->guest ? $res->guest->phone : '',
                'guest_email' => $res->guest ? $res->guest->email : '',
                'vip_level' => $res->guest ? $res->guest->vip_level : 'standard',
                'adults' => $res->adults,
                'children' => $res->children,
                'check_in_date' => $res->check_in_date,
                'check_out_date' => $res->check_out_date,
                'checked_in_at' => $res->checked_in_at ? $res->checked_in_at->format('d M Y, h:i A') : null,
                'days_remaining' => $daysRemaining,
                'is_departing_today' => $isDepartingToday,
                'folio_summary' => $folioSummary,
                'active_food_orders' => $activeFoodOrders,
                'active_spice_orders' => $activeSpiceOrders,
                'pending_extensions' => $pendingExtensions,
                'unsettled_extensions' => $unsettledExtensions,
                'active_tickets' => $activeTickets,
                'has_unread_chat' => $hasUnreadChat,
                'is_counter_booking' => (bool)$res->is_counter_booking,
                'id_proof_type' => $res->id_proof_type,
                'id_proof_number' => $res->id_proof_number,
            ];
        });

        return response()->json([
            'success' => true,
            'total_in_house' => $reservations->count(),
            'guests' => $reservations,
        ]);
    }

    /**
     * Retrieve complete 360° individual client profile and all operational logs.
     */
    public function show(int $reservationId): JsonResponse
    {
        $reservation = Reservation::with([
            'branch',
            'room',
            'roomType',
            'guest.user',
            'counterBooker',
            'folioCharges.staff',
            'facilityBookings.facility',
            'taxiRequests.folioCharge',
            'extensionRequests',
            'payments.creator'
        ])->findOrFail($reservationId);

        $guest = $reservation->guest;
        $roomId = $reservation->room_id;
        $roomNumber = $reservation->room ? $reservation->room->room_number : null;

        // 1. Food room service orders
        $foodOrders = FoodOrder::with(['items.menuItem', 'foodReview'])
            ->where(function ($q) use ($roomId, $guest) {
                if ($roomId) $q->where('room_id', $roomId);
                if ($guest) $q->orWhere('guest_id', $guest->id);
            })
            ->latest()
            ->get();

        // 2. Spice orders
        $spiceOrders = SpiceOrder::with(['items.product'])
            ->where(function ($q) use ($guest, $roomNumber) {
                if ($guest) $q->where('guest_id', $guest->id);
                if ($roomNumber) $q->orWhere('room_number', $roomNumber);
            })
            ->latest()
            ->get();

        // 3. Communications, live chat and service tickets
        $enquiries = Enquiry::with(['messages.user', 'assignedStaff', 'lockedBy'])
            ->where(function ($q) use ($reservation, $guest) {
                $q->where('reservation_id', $reservation->id);
                if ($guest) $q->orWhere('guest_id', $guest->id);
            })
            ->latest('last_message_at')
            ->get();

        // Active primary conversation
        $primaryChat = $enquiries->first(fn($e) => $e->topic === 'concierge' || empty($e->topic)) ?: $enquiries->first();

        // 4. Folio financial summary
        $folioSummary = $reservation->calculateFolioSummary();

        // Build itemized charges list
        $itemizedFolio = [];
        $itemizedFolio[] = [
            'type' => 'room_tariff',
            'category' => 'Accommodation',
            'title' => ($reservation->roomType ? $reservation->roomType->name : 'Room Stay') . " (" . Carbon::parse($reservation->check_in_date)->format('d M') . " to " . Carbon::parse($reservation->check_out_date)->format('d M') . ")",
            'date' => $reservation->check_in_date,
            'amount' => (float)$reservation->total_amount,
            'status' => $reservation->paid_amount >= $reservation->total_amount ? 'paid' : 'pending',
            'is_paid' => $reservation->paid_amount >= $reservation->total_amount,
        ];

        foreach ($reservation->folioCharges as $charge) {
            $itemizedFolio[] = [
                'id' => $charge->id,
                'type' => 'folio_charge',
                'category' => ucwords(str_replace('_', ' ', $charge->category)),
                'title' => $charge->title,
                'description' => $charge->description,
                'date' => $charge->created_at->format('Y-m-d H:i'),
                'amount' => (float)$charge->amount,
                'status' => $charge->is_paid ? 'paid' : 'pending',
                'is_paid' => (bool)$charge->is_paid,
                'staff' => $charge->staff ? $charge->staff->name : 'Staff',
            ];
        }

        return response()->json([
            'success' => true,
            'client_360' => [
                'reservation' => $reservation,
                'guest' => $guest,
                'room' => $reservation->room,
                'room_type' => $reservation->roomType,
                'branch' => $reservation->branch,
                'folio_summary' => $folioSummary,
                'itemized_folio' => $itemizedFolio,
                'payments' => $reservation->payments,
                'facility_bookings' => $reservation->facilityBookings,
                'taxi_requests' => $reservation->taxiRequests,
                'food_orders' => $foodOrders,
                'spice_orders' => $spiceOrders,
                'extension_requests' => $reservation->extensionRequests,
                'enquiries' => $enquiries,
                'primary_chat' => $primaryChat,
            ]
        ]);
    }

    /**
     * Post a custom incidental charge onto the guest's in-house room folio.
     */
    public function addFolioCharge(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::findOrFail($reservationId);

        $validated = $request->validate([
            'category' => 'required|string|in:minibar,laundry,extra_bed,transport,spa_wellness,activity,dining,miscellaneous',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:1',
        ]);

        $charge = FolioCharge::create([
            'reservation_id' => $reservation->id,
            'branch_id' => $reservation->branch_id,
            'category' => $validated['category'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'is_paid' => false,
            'added_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user() ? Auth::user()->name : 'Staff',
            'role' => Auth::user() ? Auth::user()->role : 'manager',
            'branch_id' => $reservation->branch_id,
            'action' => 'add_folio_charge',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'new_values' => $charge->toArray(),
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Folio charge of ₹" . number_format($charge->amount, 2) . " ({$charge->title}) posted to room folio.",
            'charge' => $charge,
            'folio_summary' => $reservation->calculateFolioSummary(),
        ]);
    }

    /**
     * Record on-hand or counter payment to settle in-house folio balances.
     */
    public function recordFolioPayment(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::with('folioCharges')->findOrFail($reservationId);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:cash,pos_card,counter_upi,bank_transfer',
            'notes' => 'nullable|string|max:255',
        ]);

        $amount = (float)$validated['amount'];
        $txnId = 'TXN-FOLIO-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        DB::transaction(function () use ($reservation, $amount, $validated, $txnId) {
            // 1. Record payment ledger entry
            Payment::create([
                'payable_type' => Reservation::class,
                'payable_id' => $reservation->id,
                'branch_id' => $reservation->branch_id,
                'transaction_id' => $txnId,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'gateway' => 'manual_counter',
                'status' => 'successful',
                'notes' => $validated['notes'] ?? "On-hand folio settlement recorded by " . (Auth::user() ? Auth::user()->name : 'Staff'),
                'created_by' => Auth::id(),
            ]);

            // 2. Increment paid amount
            $newPaid = (float)$reservation->paid_amount + $amount;
            $reservation->paid_amount = $newPaid;

            // 3. Mark pending folio charges as paid if covered
            $remainingPaymentPool = $amount;
            foreach ($reservation->folioCharges()->where('is_paid', false)->get() as $fc) {
                if ($remainingPaymentPool >= (float)$fc->amount) {
                    $fc->update([
                        'is_paid' => true,
                        'paid_at' => Carbon::now(),
                    ]);
                    $remainingPaymentPool -= (float)$fc->amount;
                }
            }

            // 4. Update overall payment status
            $summary = $reservation->calculateFolioSummary();
            if ($summary['net_due'] <= 0.01) {
                $reservation->payment_status = 'paid';
            } else {
                $reservation->payment_status = 'partial';
            }

            $reservation->save();
        });

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user() ? Auth::user()->name : 'Staff',
            'role' => Auth::user() ? Auth::user()->role : 'manager',
            'branch_id' => $reservation->branch_id,
            'action' => 'record_folio_payment',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'new_values' => ['amount' => $amount, 'payment_method' => $validated['payment_method'], 'transaction_id' => $txnId],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Payment of ₹" . number_format($amount, 2) . " successfully recorded (Ref: #{$txnId}).",
            'transaction_id' => $txnId,
            'folio_summary' => $reservation->fresh()->calculateFolioSummary(),
        ]);
    }

    /**
     * Send direct message or internal note to the guest from the 360° console.
     */
    public function sendMessage(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::with(['guest', 'branch'])->findOrFail($reservationId);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'is_internal_note' => 'nullable|boolean',
        ]);

        $isInternal = $request->boolean('is_internal_note', false);
        $user = Auth::user();

        // Retrieve or create active conversation
        $enquiry = Enquiry::where('reservation_id', $reservation->id)
            ->whereNotIn('status', ['closed'])
            ->latest('last_message_at')
            ->first();

        if (!$enquiry) {
            $enquiry = Enquiry::create([
                'ticket_number' => 'ENQ-' . date('Y') . '-' . strtoupper(Str::random(5)),
                'branch_id' => $reservation->branch_id,
                'guest_id' => $reservation->guest_id,
                'user_id' => $reservation->guest ? $reservation->guest->user_id : null,
                'reservation_id' => $reservation->id,
                'customer_name' => $reservation->guest ? $reservation->guest->full_name : 'Guest',
                'customer_email' => $reservation->guest ? $reservation->guest->email : null,
                'customer_phone' => $reservation->guest ? $reservation->guest->phone : null,
                'topic' => 'concierge',
                'subject' => "In-House Villa Concierge · Room " . ($reservation->room ? $reservation->room->room_number : ''),
                'status' => 'in_progress',
                'priority' => 'normal',
                'assigned_to' => Auth::id(),
                'last_message_at' => Carbon::now(),
            ]);
        }

        $enquiryMessage = EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'staff',
            'user_id' => $user ? $user->id : null,
            'message' => $validated['message'],
            'is_internal_note' => $isInternal,
        ]);

        $enquiry->update([
            'last_message_at' => Carbon::now(),
            'has_unread_messages' => false,
            'status' => 'waiting_customer',
        ]);

        // Send SMS alert to guest if not an internal note
        if (!$isInternal && $reservation->guest && $reservation->guest->phone) {
            try {
                $smsService = app(SmsNotificationService::class);
                $smsService->dispatchSms(
                    $reservation->guest->phone,
                    "Dear {$reservation->guest->first_name}, staff message from Krishna Resorts: \"{$validated['message']}\"",
                    $reservation->branch_id,
                    $enquiry->id,
                    'Guest'
                );
            } catch (\Exception $e) {
                // Non-blocking
            }
        }

        return response()->json([
            'success' => true,
            'message' => $isInternal ? 'Internal staff note recorded.' : 'Message sent to guest successfully.',
            'chat_message' => $enquiryMessage->load('user'),
        ]);
    }

    /**
     * Dispatch an in-house service ticket (Housekeeping, Linens, Maintenance, Concierge).
     */
    public function createServiceTicket(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::with(['guest', 'room'])->findOrFail($reservationId);

        $validated = $request->validate([
            'topic' => 'required|string|in:housekeeping,amenities,maintenance,concierge,billing,room_service',
            'subject' => 'required|string|max:150',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'details' => 'nullable|string|max:1000',
        ]);

        $roomStr = $reservation->room ? "Villa #{$reservation->room->room_number}" : "Room";
        $ticket = Enquiry::create([
            'ticket_number' => 'SRV-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'branch_id' => $reservation->branch_id,
            'guest_id' => $reservation->guest_id,
            'user_id' => $reservation->guest ? $reservation->guest->user_id : null,
            'reservation_id' => $reservation->id,
            'customer_name' => $reservation->guest ? $reservation->guest->full_name : 'In-House Guest',
            'customer_email' => $reservation->guest ? $reservation->guest->email : null,
            'customer_phone' => $reservation->guest ? $reservation->guest->phone : null,
            'topic' => $validated['topic'],
            'subject' => "[{$roomStr}] " . $validated['subject'],
            'status' => 'in_progress',
            'priority' => $validated['priority'],
            'assigned_to' => Auth::id(),
            'last_message_at' => Carbon::now(),
        ]);

        if (!empty($validated['details'])) {
            EnquiryMessage::create([
                'enquiry_id' => $ticket->id,
                'sender_type' => 'staff',
                'user_id' => Auth::id(),
                'message' => $validated['details'],
                'is_internal_note' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Service ticket #{$ticket->ticket_number} created and assigned.",
            'ticket' => $ticket->load(['assignedStaff', 'messages']),
        ]);
    }

    /**
     * Update service ticket status.
     */
    public function updateTicketStatus(Request $request, int $ticketId): JsonResponse
    {
        $ticket = Enquiry::findOrFail($ticketId);

        $validated = $request->validate([
            'status' => 'required|string|in:new,in_progress,waiting_customer,resolved,closed',
        ]);

        $ticket->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Ticket #{$ticket->ticket_number} status updated to '{$ticket->status}'.",
            'ticket' => $ticket,
        ]);
    }

    /**
     * Express Check-Out with outstanding balance guard and automatic room release.
     */
    public function expressCheckOut(Request $request, int $reservationId): JsonResponse
    {
        $reservation = Reservation::with(['room', 'folioCharges'])->findOrFail($reservationId);

        if ($reservation->status !== 'checked_in') {
            return response()->json([
                'success' => false,
                'message' => "Reservation is not currently checked-in (Status: {$reservation->status}).",
            ], 422);
        }

        $summary = $reservation->calculateFolioSummary();
        $netDue = $summary['net_due'];
        $forceOverride = $request->boolean('force_override', false);

        if ($netDue > 0.01 && !$forceOverride) {
            return response()->json([
                'success' => false,
                'requires_settlement' => true,
                'net_due' => $netDue,
                'message' => "Outstanding folio balance of ₹" . number_format($netDue, 2) . " detected. Please settle payment or apply manager override.",
            ], 400);
        }

        DB::transaction(function () use ($reservation) {
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
        });

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user() ? Auth::user()->name : 'Staff',
            'role' => Auth::user() ? Auth::user()->role : 'manager',
            'branch_id' => $reservation->branch_id,
            'action' => 'express_checkout',
            'entity_type' => 'Reservation',
            'entity_id' => $reservation->id,
            'new_values' => ['status' => 'checked_out', 'room_status' => 'dirty'],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Express check-out complete for {$reservation->booking_code}. Room marked available & dirty for housekeeping.",
        ]);
    }
}
