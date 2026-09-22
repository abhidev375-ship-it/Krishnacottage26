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

class TelegramNotificationService
{
    /**
     * Dispatch Telegram notification for a new room reservation.
     */
    public function sendBookingAlert(Reservation $reservation): ?NotificationLog
    {
        $guestName = $reservation->guest ? $reservation->guest->full_name : 'Guest';
        $guestPhone = $reservation->guest ? $reservation->guest->phone : 'N/A';
        $branchName = $reservation->branch ? $reservation->branch->name : 'Krishna Cottages';
        $roomTypeName = $reservation->roomType ? $reservation->roomType->name : 'Cottage Suite';
        $checkIn = Carbon::parse($reservation->check_in_date)->format('d M Y');
        $checkOut = Carbon::parse($reservation->check_out_date)->format('d M Y');
        $nights = Carbon::parse($reservation->check_in_date)->diffInDays(Carbon::parse($reservation->check_out_date)) ?: 1;
        $total = number_format($reservation->total_amount, 2);
        $paymentStatus = strtoupper($reservation->payment_status ?? 'PENDING');

        $html = "🏨 <b>NEW ROOM RESERVATION — KRISHNA COTTAGES</b>\n\n"
            . "• <b>Booking Ref:</b> <code>#{$reservation->booking_code}</code>\n"
            . "• <b>Guest:</b> " . htmlspecialchars($guestName, ENT_QUOTES) . " (" . htmlspecialchars($guestPhone, ENT_QUOTES) . ")\n"
            . "• <b>Branch:</b> " . htmlspecialchars($branchName, ENT_QUOTES) . "\n"
            . "• <b>Accommodation:</b> " . htmlspecialchars($roomTypeName, ENT_QUOTES) . "\n"
            . "• <b>Dates:</b> {$checkIn} → {$checkOut} ({$nights} night" . ($nights > 1 ? 's' : '') . ")\n"
            . "• <b>Tariff:</b> ₹{$total} [<b>{$paymentStatus}</b>]\n\n"
            . "👉 <a href=\"" . url('/admin/reservations') . "\">Open Admin Portal to review & allocate key</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'new_room_reservation',
            refType: Reservation::class,
            refId: $reservation->id,
            branchId: $reservation->branch_id
        );
    }

    /**
     * Dispatch Telegram notification for a customer enquiry or concierge chat message.
     */
    public function sendEnquiryAlert(Enquiry $enquiry, ?string $messageSnippet = null): ?NotificationLog
    {
        $snippet = $messageSnippet ? mb_strimwidth(strip_tags($messageSnippet), 0, 200, '...') : 'New enquiry submitted via web';
        $topic = strtoupper(str_replace('_', ' ', $enquiry->topic ?? 'GENERAL'));
        $branchName = $enquiry->branch ? $enquiry->branch->name : 'Central Reservations';
        $phone = $enquiry->customer_phone ? " (" . htmlspecialchars($enquiry->customer_phone, ENT_QUOTES) . ")" : '';

        $html = "💬 <b>GUEST ENQUIRY / CONCIERGE CHAT ALERT</b>\n\n"
            . "• <b>Ticket:</b> <code>#{$enquiry->ticket_number}</code>\n"
            . "• <b>Customer:</b> " . htmlspecialchars($enquiry->customer_name, ENT_QUOTES) . "{$phone}\n"
            . "• <b>Topic:</b> #{$topic}\n"
            . "• <b>Branch:</b> " . htmlspecialchars($branchName, ENT_QUOTES) . "\n"
            . "• <b>Message:</b> <i>\"" . htmlspecialchars($snippet, ENT_QUOTES) . "\"</i>\n\n"
            . "👉 <a href=\"" . url('/admin/messages') . "\">Open Concierge Desk to reply instantly</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'new_customer_enquiry',
            refType: Enquiry::class,
            refId: $enquiry->id,
            branchId: $enquiry->branch_id
        );
    }

    /**
     * Dispatch Telegram notification for kitchen dining/room service order.
     */
    public function sendFoodOrderAlert(FoodOrder $order): ?NotificationLog
    {
        $branchName = $order->branch ? $order->branch->name : 'Resort Restaurant';
        $type = strtoupper(str_replace('_', ' ', $order->order_type ?? 'ROOM SERVICE'));
        $location = $order->room_number ? "Villa {$order->room_number}" : ($order->table_number ? "Table {$order->table_number}" : 'Dining Hall');
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;
        $total = number_format($order->total_amount, 2);

        $html = "🍽️ <b>NEW KITCHEN ORDER — KRISHNA DINING</b>\n\n"
            . "• <b>Order Ref:</b> <code>#{$order->order_number}</code>\n"
            . "• <b>Type:</b> {$type} (<b>{$location}</b>)\n"
            . "• <b>Guest:</b> " . htmlspecialchars($order->customer_name, ENT_QUOTES) . "\n"
            . "• <b>Items Count:</b> {$itemCount} item(s)\n"
            . "• <b>Total Amount:</b> ₹{$total}\n\n"
            . "👉 <a href=\"" . url('/admin/food-orders') . "\">Open Kitchen Order Board to accept</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'new_food_order',
            refType: FoodOrder::class,
            refId: $order->id,
            branchId: $order->branch_id
        );
    }

    /**
     * Dispatch Telegram notification for a stay extension request.
     */
    public function sendStayExtensionAlert(StayExtensionRequest $ext, Reservation $reservation): ?NotificationLog
    {
        $guestName = $reservation->guest ? $reservation->guest->full_name : 'In-House Guest';
        $villa = $reservation->room ? "Villa {$reservation->room->room_number}" : 'In-House Villa';
        $curCheckout = Carbon::parse($ext->current_checkout_date)->format('d M');
        $reqCheckout = Carbon::parse($ext->requested_checkout_date)->format('d M Y');
        $amount = number_format($ext->standard_amount, 2);

        $html = "🛎️ <b>STAY EXTENSION REQUEST — KRISHNA COTTAGES</b>\n\n"
            . "• <b>Guest:</b> " . htmlspecialchars($guestName, ENT_QUOTES) . " (<b>{$villa}</b>)\n"
            . "• <b>Booking Ref:</b> <code>#{$reservation->booking_code}</code>\n"
            . "• <b>Current Checkout:</b> {$curCheckout}\n"
            . "• <b>Requested Extension:</b> Until {$reqCheckout} (+{$ext->extra_nights} nights)\n"
            . "• <b>Est. Tariff:</b> ₹{$amount}\n\n"
            . "👉 <a href=\"" . url('/admin/in-house') . "\">Open In-House Hub to approve offer</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'stay_extension_requested',
            refType: StayExtensionRequest::class,
            refId: $ext->id,
            branchId: $reservation->branch_id
        );
    }

    /**
     * Dispatch Telegram notification for a resort experience / facility booking.
     */
    public function sendFacilityBookingAlert(FacilityBooking $booking): ?NotificationLog
    {
        $facilityName = $booking->facility ? $booking->facility->name : 'Resort Experience';
        $guestName = $booking->guest ? $booking->guest->full_name : 'Guest';
        $date = Carbon::parse($booking->booking_date)->format('d M Y');
        $branchName = $booking->branch ? $booking->branch->name : 'Resort Concierge';
        $villa = ($booking->reservation && $booking->reservation->room) ? " (Villa {$booking->reservation->room->room_number})" : '';

        $html = "🌿 <b>EXPERIENCE & SPA BOOKING — KRISHNA CONCIERGE</b>\n\n"
            . "• <b>Experience:</b> " . htmlspecialchars($facilityName, ENT_QUOTES) . "\n"
            . "• <b>Guest:</b> " . htmlspecialchars($guestName, ENT_QUOTES) . "{$villa}\n"
            . "• <b>Scheduled Date:</b> {$date}\n"
            . "• <b>Guests Count:</b> {$booking->guests_count} person(s)\n"
            . "• <b>Branch:</b> " . htmlspecialchars($branchName, ENT_QUOTES) . "\n\n"
            . "👉 <a href=\"" . url('/admin/in-house') . "\">Open Concierge to schedule slot</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'facility_experience_booked',
            refType: FacilityBooking::class,
            refId: $booking->id,
            branchId: $booking->branch_id
        );
    }

    /**
     * Dispatch Telegram notification for an excursion taxi / cab request.
     */
    public function sendTaxiRequestAlert(TaxiRequest $taxi): ?NotificationLog
    {
        $guestName = $taxi->guest ? $taxi->guest->full_name : 'Guest';
        $villa = ($taxi->reservation && $taxi->reservation->room) ? "Villa {$taxi->reservation->room->room_number}" : 'Resort Guest';
        $pickup = Carbon::parse($taxi->pickup_date)->format('d M Y') . " at " . $taxi->pickup_time;
        $stops = $taxi->selected_locations ? $taxi->selected_locations->pluck('name')->implode(', ') : 'Custom Excursion';

        $html = "🚕 <b>CAB & EXCURSION REQUEST — KRISHNA CONCIERGE</b>\n\n"
            . "• <b>Ref:</b> <code>#{$taxi->booking_reference}</code>\n"
            . "• <b>Guest:</b> " . htmlspecialchars($guestName, ENT_QUOTES) . " (<b>{$villa}</b>)\n"
            . "• <b>Pickup:</b> {$pickup}\n"
            . "• <b>Passengers:</b> {$taxi->passengers_count} pax\n"
            . "• <b>Route / Stops:</b> " . htmlspecialchars($stops, ENT_QUOTES) . "\n\n"
            . "👉 <a href=\"" . url('/admin/in-house') . "\">Contact chauffeur & confirm fare</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'taxi_excursion_requested',
            refType: TaxiRequest::class,
            refId: $taxi->id,
            branchId: $taxi->branch_id
        );
    }

    /**
     * Dispatch Telegram notification for an online spice store purchase.
     */
    public function sendSpiceOrderAlert(SpiceOrder $order): ?NotificationLog
    {
        $mode = strtoupper($order->delivery_mode ?? 'COURIER');
        $location = ($order->delivery_mode === 'villa' && $order->room_number) ? "Villa {$order->room_number}" : "{$order->shipping_city}, {$order->shipping_state}";
        $total = number_format($order->total_amount, 2);
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;

        $html = "📦 <b>NEW SPICE ORDER — KRISHNA SPICES STORE</b>\n\n"
            . "• <b>Order Ref:</b> <code>#{$order->order_number}</code>\n"
            . "• <b>Customer:</b> " . htmlspecialchars($order->customer_name, ENT_QUOTES) . "\n"
            . "• <b>Delivery Mode:</b> {$mode} (" . htmlspecialchars($location, ENT_QUOTES) . ")\n"
            . "• <b>Quantity:</b> {$itemCount} pack(s)\n"
            . "• <b>Total Amount:</b> ₹{$total}\n\n"
            . "👉 <a href=\"" . url('/admin/spice-orders') . "\">Open Spices Desk to pack & dispatch</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'new_spice_order',
            refType: SpiceOrder::class,
            refId: $order->id,
            branchId: null
        );
    }

    /**
     * Dispatch Telegram notification for a spice return or refund request.
     */
    public function sendSpiceReturnAlert(SpiceOrder $order): ?NotificationLog
    {
        $cashback = number_format($order->refund_amount ?? 0, 2);
        $reason = mb_strimwidth($order->cancellation_reason ?? 'Customer requested cancellation/return', 0, 100, '...');

        $html = "🔄 <b>SPICE RETURN REQUEST — KRISHNA SPICES</b>\n\n"
            . "• <b>Order Ref:</b> <code>#{$order->order_number}</code>\n"
            . "• <b>Customer:</b> " . htmlspecialchars($order->customer_name, ENT_QUOTES) . "\n"
            . "• <b>Eligible Cashback:</b> ₹{$cashback}\n"
            . "• <b>Reason:</b> <i>\"" . htmlspecialchars($reason, ENT_QUOTES) . "\"</i>\n\n"
            . "👉 <a href=\"" . url('/admin/spices') . "\">Open Spices Return Desk to review</a>";

        return $this->dispatchTelegram(
            text: $html,
            event: 'spice_return_requested',
            refType: SpiceOrder::class,
            refId: $order->id,
            branchId: null
        );
    }

    /**
     * Send a test Telegram message to verify Bot Token and Chat ID.
     */
    public function sendTestMessage(string $botToken, string $chatId): array
    {
        $time = Carbon::now()->format('d M Y, h:i:s A');
        $html = "👋 <b>Namaste from Krishna Cottages & Resorts!</b>\n\n"
            . "This test ping confirms that your <b>Telegram Notification Bot</b> is active and successfully linked to this chat.\n\n"
            . "• <b>Chat ID:</b> <code>{$chatId}</code>\n"
            . "• <b>Timestamp:</b> {$time}\n\n"
            . "Real-time alerts for reservations, food orders, concierge messages, and resort operations will arrive here instantly!";

        return $this->executeTelegramApi($botToken, $chatId, $html);
    }

    /**
     * Core Telegram dispatch method with logging to notification_logs.
     */
    public function dispatchTelegram(
        string $text,
        string $event,
        ?string $refType = null,
        ?int $refId = null,
        ?int $branchId = null,
        ?string $overrideChatId = null
    ): ?NotificationLog {
        // 1. Check if Telegram notifications are enabled in settings
        $enabled = (bool) Setting::get('telegram_notifications_enabled', true);
        if (!$enabled) {
            Log::info("Telegram alert skipped: telegram_notifications_enabled is false.");
            return null;
        }

        // 2. Resolve bot token and destination chat ID
        $botToken = Setting::get('telegram_bot_token', env('TELEGRAM_BOT_TOKEN', ''));
        $chatId = $overrideChatId ?: Setting::get('telegram_chat_id', env('TELEGRAM_CHAT_ID', ''));

        $status = 'pending';
        $failureReason = null;

        if (!empty($botToken) && !empty($chatId)) {
            $apiResult = $this->executeTelegramApi($botToken, $chatId, $text);
            $status = $apiResult['status'];
            $failureReason = $apiResult['error'];
        } else {
            $status = 'pending';
            $failureReason = 'Telegram Bot Token or Chat ID not configured in System Settings.';
            Log::info("Telegram not configured. Simulated notification: " . mb_strimwidth(strip_tags($text), 0, 100));
        }

        // 3. Record in immutable notification_logs table (channel = 'telegram')
        try {
            return NotificationLog::create([
                'event' => $event,
                'channel' => 'telegram',
                'recipient' => $chatId ?: 'Unconfigured Telegram Chat ID',
                'branch_id' => $branchId,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'subject' => 'Telegram Operational Alert',
                'message_body' => $text,
                'status' => ($status === 'delivered' ? 'delivered' : ($status === 'failed' ? 'failed' : 'pending')),
                'failure_reason' => $failureReason,
                'sent_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to log Telegram notification to notification_logs: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Execute raw HTTP request to Telegram Bot API.
     */
    protected function executeTelegramApi(string $botToken, string $chatId, string $text): array
    {
        $cleanToken = trim($botToken);
        $cleanChatId = trim($chatId);

        $url = "https://api.telegram.org/bot{$cleanToken}/sendMessage";

        try {
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $cleanChatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
            ]);

            $json = $response->json();

            if ($response->successful() && !empty($json['ok'])) {
                return [
                    'success' => true,
                    'status' => 'delivered',
                    'error' => null,
                ];
            }

            $errMsg = $json['description'] ?? ('Telegram HTTP ' . $response->status() . ': ' . $response->body());
            Log::warning("Telegram Bot API Error: {$errMsg}");

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $errMsg,
            ];
        } catch (\Throwable $e) {
            Log::warning("Telegram API HTTP Exception: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
