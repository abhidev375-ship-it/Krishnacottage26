<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\FacilityBooking;
use App\Models\FoodOrder;
use App\Models\NotificationLog;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\SpiceOrder;
use App\Models\StayExtensionRequest;
use App\Models\TaxiRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Dispatch WhatsApp notification for a new room reservation.
     */
    public function sendBookingAlert(Reservation $reservation): ?NotificationLog
    {
        $guestName = $reservation->guest ? $reservation->guest->full_name : 'Guest';
        $branchName = $reservation->branch ? $reservation->branch->name : 'Krishna Resorts';
        $roomTypeName = $reservation->roomType ? $reservation->roomType->name : 'Resort Suite';
        $checkIn = Carbon::parse($reservation->check_in_date)->format('d M Y');
        $checkOut = Carbon::parse($reservation->check_out_date)->format('d M Y');
        $nights = Carbon::parse($reservation->check_in_date)->diffInDays(Carbon::parse($reservation->check_out_date)) ?: 1;
        $total = number_format($reservation->total_amount, 2);
        $paymentStatus = strtoupper($reservation->payment_status ?? 'PENDING');

        $text = "🏨 *NEW ROOM RESERVATION — KRISHNA RESORTS*\n\n"
            . "• *Booking Ref:* #{$reservation->booking_code}\n"
            . "• *Guest:* {$guestName}\n"
            . "• *Branch:* {$branchName}\n"
            . "• *Accommodation:* {$roomTypeName}\n"
            . "• *Stay Dates:* {$checkIn} → {$checkOut} ({$nights} night" . ($nights > 1 ? 's' : '') . ")\n"
            . "• *Total Tariff:* ₹{$total} [{$paymentStatus}]\n\n"
            . "👉 Open Admin Panel to review & allocate room key.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'new_room_reservation',
            refType: Reservation::class,
            refId: $reservation->id,
            branchId: $reservation->branch_id
        );
    }

    /**
     * Dispatch WhatsApp notification for a new customer enquiry or chat message.
     */
    public function sendEnquiryAlert(Enquiry $enquiry, ?string $messageSnippet = null): ?NotificationLog
    {
        $snippet = $messageSnippet ? mb_strimwidth(strip_tags($messageSnippet), 0, 100, '...') : 'New enquiry submitted via web';
        $topic = strtoupper(str_replace('_', ' ', $enquiry->topic ?? 'GENERAL'));
        $branchName = $enquiry->branch ? $enquiry->branch->name : 'Central Reservations';

        $text = "💬 *NEW GUEST ENQUIRY — KRISHNA RESORTS*\n\n"
            . "• *Ticket:* #{$enquiry->ticket_number}\n"
            . "• *Customer:* {$enquiry->customer_name}\n"
            . "• *Topic:* #{$topic}\n"
            . "• *Branch Scope:* {$branchName}\n"
            . "• *Message:* \"{$snippet}\"\n\n"
            . "👉 Open Admin Chat to reply instantly.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'new_customer_enquiry',
            refType: Enquiry::class,
            refId: $enquiry->id,
            branchId: $enquiry->branch_id
        );
    }

    /**
     * Dispatch WhatsApp notification for a kitchen dining/room service order.
     */
    public function sendFoodOrderAlert(FoodOrder $order): ?NotificationLog
    {
        $branchName = $order->branch ? $order->branch->name : 'Resort Restaurant';
        $type = strtoupper(str_replace('_', ' ', $order->order_type ?? 'ROOM SERVICE'));
        $location = $order->room_number ? "Villa {$order->room_number}" : ($order->table_number ? "Table {$order->table_number}" : 'Dining Hall');
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;
        $total = number_format($order->total_amount, 2);

        $text = "🍽️ *NEW KITCHEN ORDER — KRISHNA DINING*\n\n"
            . "• *Order:* #{$order->order_number}\n"
            . "• *Type:* {$type} ({$location})\n"
            . "• *Customer:* {$order->customer_name}\n"
            . "• *Items Count:* {$itemCount} item(s)\n"
            . "• *Order Total:* ₹{$total}\n\n"
            . "👉 Open Kitchen Order Board to accept & prepare.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'new_food_order',
            refType: FoodOrder::class,
            refId: $order->id,
            branchId: $order->branch_id
        );
    }

    /**
     * Dispatch WhatsApp notification for a stay extension request.
     */
    public function sendStayExtensionAlert(StayExtensionRequest $ext, Reservation $reservation): ?NotificationLog
    {
        $guestName = $reservation->guest ? $reservation->guest->full_name : 'In-House Guest';
        $villa = $reservation->room ? "Villa {$reservation->room->room_number}" : 'In-House Villa';
        $curCheckout = Carbon::parse($ext->current_checkout_date)->format('d M');
        $reqCheckout = Carbon::parse($ext->requested_checkout_date)->format('d M Y');
        $amount = number_format($ext->standard_amount, 2);

        $text = "🛎️ *HOLIDAY EXTENSION REQUEST — KRISHNA RESORTS*\n\n"
            . "• *Guest:* {$guestName} ({$villa})\n"
            . "• *Booking Ref:* #{$reservation->booking_code}\n"
            . "• *Current Checkout:* {$curCheckout}\n"
            . "• *Requested Extension:* Until {$reqCheckout} (+{$ext->extra_nights} nights)\n"
            . "• *Estimated Amount:* ₹{$amount}\n\n"
            . "👉 Open In-House Hub to approve discount rate offer.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'stay_extension_requested',
            refType: StayExtensionRequest::class,
            refId: $ext->id,
            branchId: $reservation->branch_id
        );
    }

    /**
     * Dispatch WhatsApp notification for a resort experience / facility booking.
     */
    public function sendFacilityBookingAlert(FacilityBooking $booking): ?NotificationLog
    {
        $facilityName = $booking->facility ? $booking->facility->name : 'Resort Experience';
        $guestName = $booking->guest ? $booking->guest->full_name : 'Guest';
        $date = Carbon::parse($booking->booking_date)->format('d M Y');
        $branchName = $booking->branch ? $booking->branch->name : 'Resort Concierge';
        $villa = ($booking->reservation && $booking->reservation->room) ? " (Villa {$booking->reservation->room->room_number})" : '';

        $text = "🌿 *EXPERIENCE & SPA BOOKING — KRISHNA CONCIERGE*\n\n"
            . "• *Experience:* {$facilityName}\n"
            . "• *Guest:* {$guestName}{$villa}\n"
            . "• *Scheduled Date:* {$date}\n"
            . "• *Guests Count:* {$booking->guests_count} person(s)\n"
            . "• *Resort Branch:* {$branchName}\n\n"
            . "👉 Open Concierge Desk to allocate time slot & guide.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'facility_experience_booked',
            refType: FacilityBooking::class,
            refId: $booking->id,
            branchId: $booking->branch_id
        );
    }

    /**
     * Dispatch WhatsApp notification for an excursion taxi / cab request.
     */
    public function sendTaxiRequestAlert(TaxiRequest $taxi): ?NotificationLog
    {
        $guestName = $taxi->guest ? $taxi->guest->full_name : 'Guest';
        $villa = ($taxi->reservation && $taxi->reservation->room) ? "Villa {$taxi->reservation->room->room_number}" : 'Resort Guest';
        $pickup = Carbon::parse($taxi->pickup_date)->format('d M Y') . " at " . $taxi->pickup_time;
        $stops = $taxi->selected_locations ? $taxi->selected_locations->pluck('name')->implode(', ') : 'Custom Excursion';

        $text = "🚕 *CAB & EXCURSION REQUEST — KRISHNA CONCIERGE*\n\n"
            . "• *Ref:* #{$taxi->booking_reference}\n"
            . "• *Guest:* {$guestName} ({$villa})\n"
            . "• *Pickup:* {$pickup}\n"
            . "• *Passengers:* {$taxi->passengers_count} pax\n"
            . "• *Route / Stops:* {$stops}\n\n"
            . "👉 Contact chauffeur & confirm rate with guest.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'taxi_excursion_requested',
            refType: TaxiRequest::class,
            refId: $taxi->id,
            branchId: $taxi->branch_id
        );
    }

    /**
     * Dispatch WhatsApp notification for an online spice store purchase.
     */
    public function sendSpiceOrderAlert(SpiceOrder $order): ?NotificationLog
    {
        $mode = strtoupper($order->delivery_mode ?? 'COURIER');
        $location = ($order->delivery_mode === 'villa' && $order->room_number) ? "Villa {$order->room_number}" : "{$order->shipping_city}, {$order->shipping_state}";
        $total = number_format($order->total_amount, 2);
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;

        $text = "📦 *NEW SPICE ORDER — KRISHNA SPICES STORE*\n\n"
            . "• *Order Ref:* #{$order->order_number}\n"
            . "• *Customer:* {$order->customer_name}\n"
            . "• *Delivery Mode:* {$mode} ({$location})\n"
            . "• *Quantity:* {$itemCount} pack(s)\n"
            . "• *Total Amount:* ₹{$total}\n\n"
            . "👉 Open Spices Desk to pack & dispatch.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'new_spice_order',
            refType: SpiceOrder::class,
            refId: $order->id,
            branchId: null
        );
    }

    /**
     * Dispatch WhatsApp notification for a spice return or refund request.
     */
    public function sendSpiceReturnAlert(SpiceOrder $order): ?NotificationLog
    {
        $cashback = number_format($order->refund_amount ?? 0, 2);
        $reason = mb_strimwidth($order->cancellation_reason ?? 'Customer requested cancellation/return', 0, 80, '...');

        $text = "🔄 *SPICE RETURN REQUEST — KRISHNA SPICES*\n\n"
            . "• *Order Ref:* #{$order->order_number}\n"
            . "• *Customer:* {$order->customer_name}\n"
            . "• *Eligible Cashback:* ₹{$cashback}\n"
            . "• *Reason:* \"{$reason}\"\n\n"
            . "👉 Open Spices Return Policy tab to review & approve.";

        return $this->dispatchWhatsApp(
            text: $text,
            event: 'spice_return_requested',
            refType: SpiceOrder::class,
            refId: $order->id,
            branchId: null
        );
    }

    /**
     * Send a test WhatsApp ping to verify CallMeBot credentials.
     */
    public function sendTestMessage(string $phone, string $apiKey): array
    {
        $text = "👋 *Namaste from Krishna Resorts & Spices!*\n\n"
            . "This is a test notification confirming that your *CallMeBot WhatsApp Service* is fully connected and ready to receive real-time resort operational alerts!\n\n"
            . "Timestamp: " . Carbon::now()->format('d M Y, h:i A');

        return $this->executeCallMeBotApi($phone, $text, $apiKey);
    }

    /**
     * Core CallMeBot dispatch method with logging to notification_logs.
     */
    public function dispatchWhatsApp(
        string $text,
        string $event,
        ?string $refType = null,
        ?int $refId = null,
        ?int $branchId = null,
        ?string $overridePhone = null
    ): ?NotificationLog {
        // 1. Check if WhatsApp notifications are enabled in settings
        $enabled = (bool) Setting::get('whatsapp_notifications_enabled', true);
        if (!$enabled) {
            Log::info("WhatsApp alert skipped: whatsapp_notifications_enabled is false.");
            return null;
        }

        // 2. Resolve destination phone & API key
        $phone = $overridePhone ?: Setting::get('callmebot_phone', env('CALLMEBOT_PHONE', ''));
        $apiKey = Setting::get('callmebot_apikey', env('CALLMEBOT_APIKEY', ''));

        // Clean phone number (must include international code e.g. +91...)
        $cleanPhone = preg_replace('/[^\+0-9]/', '', (string) $phone);

        $status = 'pending';
        $failureReason = null;

        if (!empty($cleanPhone) && !empty($apiKey)) {
            $apiResult = $this->executeCallMeBotApi($cleanPhone, $text, $apiKey);
            $status = $apiResult['status'];
            $failureReason = $apiResult['error'];
        } else {
            // Unconfigured: log as pending with informative reason
            $status = 'pending';
            $failureReason = 'CallMeBot phone or API key not configured in System Settings.';
            Log::info("CallMeBot not configured. Simulated WhatsApp notification: " . mb_strimwidth($text, 0, 100));
        }

        // 3. Record in immutable notification_logs table (channel = 'whatsapp')
        try {
            return NotificationLog::create([
                'event' => $event,
                'channel' => 'whatsapp',
                'recipient' => $cleanPhone ?: 'Unconfigured Manager Phone',
                'branch_id' => $branchId,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'subject' => 'CallMeBot WhatsApp Operational Alert',
                'message_body' => $text,
                'status' => ($status === 'delivered' ? 'delivered' : ($status === 'failed' ? 'failed' : 'pending')),
                'failure_reason' => $failureReason,
                'sent_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to log WhatsApp notification to notification_logs: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Raw CallMeBot HTTP request execution.
     */
    protected function executeCallMeBotApi(string $phone, string $text, string $apiKey): array
    {
        $cleanPhone = preg_replace('/[^\+0-9]/', '', $phone);
        // CallMeBot supports phone with international code
        $phoneParam = ltrim($cleanPhone, '+');

        $url = "https://api.callmebot.com/whatsapp.php";

        try {
            $response = Http::timeout(8)->get($url, [
                'phone' => $phoneParam,
                'text' => $text,
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                $body = $response->body();
                // CallMeBot returns messages containing 'Message queued' or 'success'
                if (stripos($body, 'error') !== false || stripos($body, 'invalid') !== false) {
                    return [
                        'success' => false,
                        'status' => 'failed',
                        'error' => strip_tags($body) ?: 'CallMeBot returned an error response.',
                    ];
                }

                return [
                    'success' => true,
                    'status' => 'delivered',
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'CallMeBot HTTP ' . $response->status() . ': ' . strip_tags($response->body()),
            ];
        } catch (\Throwable $e) {
            Log::warning("CallMeBot HTTP Exception: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
