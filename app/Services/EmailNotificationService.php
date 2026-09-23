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
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Dispatch email notification for a new room reservation.
     */
    public function sendBookingAlert(Reservation $reservation): ?NotificationLog
    {
        $guestName = $reservation->guest ? $reservation->guest->full_name : 'Guest';
        $guestEmail = $reservation->guest ? $reservation->guest->email : 'N/A';
        $guestPhone = $reservation->guest ? $reservation->guest->phone : 'N/A';
        $branchName = $reservation->branch ? $reservation->branch->name : 'Krishna Cottages';
        $roomTypeName = $reservation->roomType ? $reservation->roomType->name : 'Cottage Suite';
        $checkIn = Carbon::parse($reservation->check_in_date)->format('d M Y');
        $checkOut = Carbon::parse($reservation->check_out_date)->format('d M Y');
        $nights = Carbon::parse($reservation->check_in_date)->diffInDays(Carbon::parse($reservation->check_out_date)) ?: 1;
        $total = number_format($reservation->total_amount, 2);
        $paymentStatus = strtoupper($reservation->payment_status ?? 'PENDING');

        $subject = "🏨 New Room Reservation #{$reservation->booking_code} — {$guestName}";

        $rows = [
            'Booking Reference' => "#{$reservation->booking_code}",
            'Guest Name' => $guestName,
            'Guest Contact' => "{$guestPhone} | {$guestEmail}",
            'Branch / Property' => $branchName,
            'Room Category' => $roomTypeName,
            'Stay Schedule' => "{$checkIn} → {$checkOut} ({$nights} night" . ($nights > 1 ? 's' : '') . ")",
            'Tariff Total' => "₹{$total} ({$paymentStatus})",
        ];

        $html = $this->buildHtmlTemplate(
            title: "New Room Reservation Received",
            badge: "ROOM RESERVATION",
            badgeColor: "#059669",
            lead: "A new guest reservation has been booked via the online portal.",
            rows: $rows,
            actionUrl: url('/admin/reservations'),
            actionText: "View Reservation & Allocate Key"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'new_room_reservation',
            refType: Reservation::class,
            refId: $reservation->id,
            branchId: $reservation->branch_id
        );
    }

    /**
     * Dispatch email notification for a customer enquiry or concierge chat message.
     */
    public function sendEnquiryAlert(Enquiry $enquiry, ?string $messageSnippet = null): ?NotificationLog
    {
        $snippet = $messageSnippet ? mb_strimwidth(strip_tags($messageSnippet), 0, 300, '...') : 'New enquiry submitted via web';
        $topic = strtoupper(str_replace('_', ' ', $enquiry->topic ?? 'GENERAL'));
        $branchName = $enquiry->branch ? $enquiry->branch->name : 'Central Reservations';

        $subject = "💬 Guest Chat / Enquiry Alert #{$enquiry->ticket_number} — {$enquiry->customer_name}";

        $rows = [
            'Ticket Number' => "#{$enquiry->ticket_number}",
            'Customer Name' => $enquiry->customer_name,
            'Customer Phone' => $enquiry->customer_phone ?: 'Not provided',
            'Customer Email' => $enquiry->customer_email ?: 'Not provided',
            'Inquiry Topic' => "#{$topic}",
            'Branch Scope' => $branchName,
            'Latest Message' => nl2br(e($snippet)),
        ];

        $html = $this->buildHtmlTemplate(
            title: "Guest Inquiry / Concierge Message",
            badge: "GUEST CONCIERGE",
            badgeColor: "#d97706",
            lead: "A guest has contacted the concierge desk or submitted an inquiry ticket.",
            rows: $rows,
            actionUrl: url('/admin/messages'),
            actionText: "Open Concierge Desk & Reply"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'new_customer_enquiry',
            refType: Enquiry::class,
            refId: $enquiry->id,
            branchId: $enquiry->branch_id
        );
    }

    /**
     * Dispatch email notification for kitchen dining/room service order.
     */
    public function sendFoodOrderAlert(FoodOrder $order): ?NotificationLog
    {
        $branchName = $order->branch ? $order->branch->name : 'Resort Restaurant';
        $type = strtoupper(str_replace('_', ' ', $order->order_type ?? 'ROOM SERVICE'));
        $location = $order->room_number ? "Villa {$order->room_number}" : ($order->table_number ? "Table {$order->table_number}" : 'Dining Hall');
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;
        $total = number_format($order->total_amount, 2);

        $itemsSummary = '';
        if ($order->items && $order->items->count()) {
            foreach ($order->items as $it) {
                $name = $it->menuItem ? $it->menuItem->name : 'Menu Item';
                $itemsSummary .= "• {$it->quantity}x {$name} (₹" . number_format($it->unit_price * $it->quantity, 2) . ")<br>";
            }
        }

        $subject = "🍽️ Kitchen Order #{$order->order_number} ({$location}) — ₹{$total}";

        $rows = [
            'Order Number' => "#{$order->order_number}",
            'Service Type' => "{$type} [{$location}]",
            'Guest Name' => $order->customer_name,
            'Total Items' => "{$itemCount} item(s)",
            'Order Value' => "₹{$total}",
            'Ordered Items' => $itemsSummary ?: 'Standard menu selection',
        ];

        $html = $this->buildHtmlTemplate(
            title: "New Kitchen Dining Order",
            badge: "KITCHEN ORDER",
            badgeColor: "#ea580c",
            lead: "A new kitchen / room-service order has been submitted.",
            rows: $rows,
            actionUrl: url('/admin/food-orders'),
            actionText: "Open Kitchen Board"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'new_food_order',
            refType: FoodOrder::class,
            refId: $order->id,
            branchId: $order->branch_id
        );
    }

    /**
     * Dispatch email notification for a stay extension request.
     */
    public function sendStayExtensionAlert(StayExtensionRequest $ext, Reservation $reservation): ?NotificationLog
    {
        $guestName = $reservation->guest ? $reservation->guest->full_name : 'In-House Guest';
        $villa = $reservation->room ? "Villa {$reservation->room->room_number}" : 'In-House Suite';
        $curCheckout = Carbon::parse($ext->current_checkout_date)->format('d M Y');
        $reqCheckout = Carbon::parse($ext->requested_checkout_date)->format('d M Y');
        $amount = number_format($ext->standard_amount, 2);

        $subject = "🛎️ Stay Extension Request (+{$ext->extra_nights} Nights) — {$guestName} ({$villa})";

        $rows = [
            'Booking Code' => "#{$reservation->booking_code}",
            'Guest' => "{$guestName} ({$villa})",
            'Current Checkout' => $curCheckout,
            'Requested Checkout' => "{$reqCheckout} (+{$ext->extra_nights} nights)",
            'Standard Tariff' => "₹{$amount}",
            'Guest Note' => $ext->notes ?: 'None provided',
        ];

        $html = $this->buildHtmlTemplate(
            title: "Guest Stay Extension Request",
            badge: "STAY EXTENSION",
            badgeColor: "#2563eb",
            lead: "An in-house guest has requested to extend their holiday stay.",
            rows: $rows,
            actionUrl: url('/admin/in-house'),
            actionText: "Review Extension in In-House Hub"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'stay_extension_requested',
            refType: StayExtensionRequest::class,
            refId: $ext->id,
            branchId: $reservation->branch_id
        );
    }

    /**
     * Dispatch email notification for a resort experience / facility booking.
     */
    public function sendFacilityBookingAlert(FacilityBooking $booking): ?NotificationLog
    {
        $facilityName = $booking->facility ? $booking->facility->name : 'Resort Experience';
        $guestName = $booking->guest ? $booking->guest->full_name : 'Guest';
        $date = Carbon::parse($booking->booking_date)->format('d M Y');
        $branchName = $booking->branch ? $booking->branch->name : 'Resort Concierge';
        $villa = ($booking->reservation && $booking->reservation->room) ? " (Villa {$booking->reservation->room->room_number})" : '';

        $subject = "🌿 Experience Booking: {$facilityName} — {$guestName}{$villa}";

        $rows = [
            'Experience' => $facilityName,
            'Guest' => "{$guestName}{$villa}",
            'Scheduled Date' => $date,
            'Guests Count' => "{$booking->guests_count} person(s)",
            'Total Charge' => "₹" . number_format($booking->total_amount, 2),
            'Branch' => $branchName,
        ];

        $html = $this->buildHtmlTemplate(
            title: "Resort Experience Booking",
            badge: "FACILITY BOOKING",
            badgeColor: "#0d9488",
            lead: "A guest has booked an on-site facility / resort experience.",
            rows: $rows,
            actionUrl: url('/admin/in-house'),
            actionText: "Allocate Slot in Concierge"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'facility_experience_booked',
            refType: FacilityBooking::class,
            refId: $booking->id,
            branchId: $booking->branch_id
        );
    }

    /**
     * Dispatch email notification for an excursion taxi / cab request.
     */
    public function sendTaxiRequestAlert(TaxiRequest $taxi): ?NotificationLog
    {
        $guestName = $taxi->guest ? $taxi->guest->full_name : 'Guest';
        $villa = ($taxi->reservation && $taxi->reservation->room) ? "Villa {$taxi->reservation->room->room_number}" : 'Resort Guest';
        $pickup = Carbon::parse($taxi->pickup_date)->format('d M Y') . " at " . $taxi->pickup_time;
        $stops = $taxi->selected_locations ? $taxi->selected_locations->pluck('name')->implode(', ') : 'Custom Excursion';

        $subject = "🚕 Cab Excursion Request #{$taxi->booking_reference} — {$guestName}";

        $rows = [
            'Reference' => "#{$taxi->booking_reference}",
            'Guest' => "{$guestName} ({$villa})",
            'Pickup Schedule' => $pickup,
            'Passenger Count' => "{$taxi->passengers_count} pax",
            'Itinerary / Stops' => $stops,
        ];

        $html = $this->buildHtmlTemplate(
            title: "Cab / Excursion Booking Request",
            badge: "TAXI EXCURSION",
            badgeColor: "#4f46e5",
            lead: "A guest has requested taxi chauffeur dispatch for local excursion.",
            rows: $rows,
            actionUrl: url('/admin/in-house'),
            actionText: "Manage Cab Request"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'taxi_excursion_requested',
            refType: TaxiRequest::class,
            refId: $taxi->id,
            branchId: $taxi->branch_id
        );
    }

    /**
     * Dispatch email notification for an online spice store purchase.
     */
    public function sendSpiceOrderAlert(SpiceOrder $order): ?NotificationLog
    {
        $mode = strtoupper($order->delivery_mode ?? 'COURIER');
        $location = ($order->delivery_mode === 'villa' && $order->room_number) ? "Villa {$order->room_number}" : "{$order->shipping_city}, {$order->shipping_state}";
        $total = number_format($order->total_amount, 2);
        $itemCount = $order->items ? $order->items->sum('quantity') : 1;

        $subject = "📦 Spice Store Order #{$order->order_number} — ₹{$total}";

        $rows = [
            'Order Reference' => "#{$order->order_number}",
            'Customer' => $order->customer_name,
            'Contact' => "{$order->customer_phone} | {$order->customer_email}",
            'Delivery Mode' => "{$mode} ({$location})",
            'Packs Quantity' => "{$itemCount} pack(s)",
            'Order Amount' => "₹{$total}",
        ];

        $html = $this->buildHtmlTemplate(
            title: "New Spice Store Order Received",
            badge: "SPICE STORE",
            badgeColor: "#059669",
            lead: "A customer placed an order on Krishna Spices Store.",
            rows: $rows,
            actionUrl: url('/admin/spice-orders'),
            actionText: "Open Spices Fulfillment Desk"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'new_spice_order',
            refType: SpiceOrder::class,
            refId: $order->id,
            branchId: null
        );
    }

    /**
     * Dispatch email notification for a spice return or refund request.
     */
    public function sendSpiceReturnAlert(SpiceOrder $order): ?NotificationLog
    {
        $cashback = number_format($order->refund_amount ?? 0, 2);
        $reason = $order->cancellation_reason ?? 'Customer requested cancellation/return';

        $subject = "🔄 Spice Return Request #{$order->order_number} — ₹{$cashback}";

        $rows = [
            'Order Reference' => "#{$order->order_number}",
            'Customer' => $order->customer_name,
            'Eligible Cashback' => "₹{$cashback}",
            'Return Reason' => $reason,
        ];

        $html = $this->buildHtmlTemplate(
            title: "Spice Order Return Request",
            badge: "RETURN REQUEST",
            badgeColor: "#e11d48",
            lead: "A customer requested a return/refund for their spice order.",
            rows: $rows,
            actionUrl: url('/admin/spices'),
            actionText: "Review Return Policy Desk"
        );

        return $this->dispatchEmail(
            subject: $subject,
            htmlBody: $html,
            event: 'spice_return_requested',
            refType: SpiceOrder::class,
            refId: $order->id,
            branchId: null
        );
    }

    /**
     * Send email via Brevo REST API (HTTPS Port 443 - 100% cloud firewall proof on Railway).
     */
    public function sendViaBrevoApi(string $apiKey, string $recipient, string $subject, string $htmlBody, ?string $fromAddress = null, ?string $fromName = null): array
    {
        $senderEmail = $fromAddress ?: Setting::get('smtp_from_address', env('MAIL_FROM_ADDRESS', 'krishnacottage2023@gmail.com'));
        $senderName = $fromName ?: Setting::get('smtp_from_name', env('MAIL_FROM_NAME', 'Krishna Cottages'));

        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $senderEmail,
            ],
            'to' => [
                [
                    'email' => $recipient,
                    'name' => 'Admin Recipient',
                ],
            ],
            'subject' => $subject,
            'htmlContent' => $htmlBody,
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'api-key' => trim($apiKey),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'delivered',
                    'error' => null,
                    'recipient' => $recipient,
                    'message_id' => $response->json('messageId'),
                ];
            }

            $msg = $response->json('message') ?? ('Brevo API error (' . $response->status() . '): ' . $response->body());
            Log::warning("Brevo API Email Error: " . $msg);
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $msg,
            ];
        } catch (\Throwable $e) {
            Log::warning("Brevo API Connection Error: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Brevo HTTPS API Connection Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send email via Resend REST API (HTTPS Port 443).
     */
    public function sendViaResendApi(string $apiKey, string $recipient, string $subject, string $htmlBody, ?string $fromAddress = null, ?string $fromName = null): array
    {
        $senderEmail = $fromAddress ?: 'onboarding@resend.dev';
        $senderName = $fromName ?: 'Krishna Cottages';

        $payload = [
            'from' => "{$senderName} <{$senderEmail}>",
            'to' => [$recipient],
            'subject' => $subject,
            'html' => $htmlBody,
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . trim($apiKey),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.resend.com/emails', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'delivered',
                    'error' => null,
                    'recipient' => $recipient,
                    'message_id' => $response->json('id'),
                ];
            }

            $msg = $response->json('message') ?? ('Resend API error (' . $response->status() . '): ' . $response->body());
            Log::warning("Resend API Email Error: " . $msg);
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $msg,
            ];
        } catch (\Throwable $e) {
            Log::warning("Resend API Connection Error: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Resend HTTPS API Connection Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send a test email to verify SMTP credentials and destination inbox.
     */
    public function sendTestEmail(array $config, string $recipientEmail): array
    {
        $host = strtolower(trim($config['smtp_host'] ?? ''));
        $password = trim($config['smtp_password'] ?? '');
        $fromAddress = $config['smtp_from_address'] ?? null;
        $fromName = $config['smtp_from_name'] ?? null;

        $now = Carbon::now()->format('d M Y, h:i:s A');

        // 1. Check for direct Resend API
        if (str_starts_with($password, 're_') || str_contains($host, 'resend')) {
            $rows = [
                'Gateway Driver' => 'Resend REST API (HTTPS Port 443)',
                'Sender Identity' => ($fromName ?? 'Krishna Cottages') . " <" . ($fromAddress ?? 'onboarding@resend.dev') . ">",
                'Recipient Address' => $recipientEmail,
                'Timestamp' => $now,
            ];
            $html = $this->buildHtmlTemplate(
                title: "Resend Email Gateway Connected!",
                badge: "TEST PASSED",
                badgeColor: "#059669",
                lead: "This test email confirms that your Resend API integration is active.",
                rows: $rows,
                actionUrl: url('/admin'),
                actionText: "Open Krishna Admin Portal"
            );
            return $this->sendViaResendApi($password, $recipientEmail, "✅ Resend API Test — Krishna Cottages", $html, $fromAddress, $fromName);
        }

        // 2. Check for direct Brevo HTTPS API
        if ($host === 'api.brevo.com' || str_starts_with($password, 'xkeysib-') || (str_contains($host, 'brevo') && (empty($config['smtp_port']) || (int)$config['smtp_port'] === 443))) {
            $rows = [
                'Gateway Driver' => 'Brevo REST API (HTTPS Port 443 - Railway Firewall Bypassed)',
                'Sender Identity' => ($fromName ?? 'Krishna Cottages') . " <" . ($fromAddress ?? 'noreply') . ">",
                'Recipient Address' => $recipientEmail,
                'Timestamp' => $now,
            ];
            $html = $this->buildHtmlTemplate(
                title: "Brevo Email Gateway Connected!",
                badge: "TEST PASSED",
                badgeColor: "#059669",
                lead: "This test email confirms that your Brevo HTTPS API connection is working seamlessly on Railway.",
                rows: $rows,
                actionUrl: url('/admin'),
                actionText: "Open Krishna Admin Portal"
            );
            return $this->sendViaBrevoApi($password, $recipientEmail, "✅ Brevo HTTPS API Test — Krishna Cottages & Resorts", $html, $fromAddress, $fromName);
        }

        // 3. Fall back to standard SMTP transport with automatic Brevo HTTPS fallback on timeout
        $configuredFrom = $this->configureSmtp($config);
        if (!$configuredFrom) {
            return [
                'success' => false,
                'status' => 'failed',
                'error' => 'Missing SMTP Host, Username or Password.',
            ];
        }

        $subject = "✅ SMTP Email Test — Krishna Cottages & Resorts";

        $rows = [
            'Test Status' => 'SMTP Handshake Successful',
            'Configured Host' => $config['smtp_host'] . ':' . ($config['smtp_port'] ?? 587),
            'Encryption' => strtoupper($config['smtp_encryption'] ?? 'TLS'),
            'Sender Identity' => ($fromName ?? 'Krishna Cottages') . " <" . ($fromAddress ?? 'noreply') . ">",
            'Recipient Address' => $recipientEmail,
            'Timestamp' => $now,
        ];

        $html = $this->buildHtmlTemplate(
            title: "SMTP Notification Gateway Connected!",
            badge: "TEST PASSED",
            badgeColor: "#059669",
            lead: "This test email confirms that your SMTP mail server credentials are valid and notifications are active.",
            rows: $rows,
            actionUrl: url('/admin'),
            actionText: "Open Krishna Admin Portal"
        );

        try {
            Mail::html($html, function ($message) use ($recipientEmail, $subject, $config) {
                $message->to($recipientEmail)
                    ->subject($subject);

                if (!empty($config['smtp_from_address'])) {
                    $message->from($config['smtp_from_address'], $config['smtp_from_name'] ?? 'Krishna Cottages');
                }
            });

            return [
                'success' => true,
                'status' => 'delivered',
                'error' => null,
                'recipient' => $recipientEmail,
            ];
        } catch (\Throwable $e) {
            Log::warning("SMTP Test Email Error: " . $e->getMessage());

            // If standard SMTP timed out and host or key is Brevo, automatically fallback to HTTPS API
            if (str_contains($host, 'brevo') || str_starts_with($password, 'xsmtpsib-') || str_starts_with($password, 'xkeysib-')) {
                Log::info("Railway blocked raw SMTP port; automatically attempting Brevo HTTPS API fallback...");
                $apiResult = $this->sendViaBrevoApi($password, $recipientEmail, "✅ Brevo HTTPS API Test — Krishna Cottages & Resorts", $html, $fromAddress, $fromName);
                if ($apiResult['success']) {
                    return $apiResult;
                }
            }

            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage() . (str_contains($e->getMessage(), 'timed out') ? ' (Railway blocks raw SMTP ports 25, 465, 587, 2525. Enter api.brevo.com as Host or use a Brevo API Key on Port 443).' : ''),
            ];
        }
    }

    /**
     * Core email dispatch method with database logging to notification_logs.
     */
    public function dispatchEmail(
        string $subject,
        string $htmlBody,
        string $event,
        ?string $refType = null,
        ?int $refId = null,
        ?int $branchId = null,
        ?string $overrideRecipient = null
    ): ?NotificationLog {
        // 1. Check if email notifications are enabled
        $enabled = (bool) Setting::get('smtp_notifications_enabled', true);
        if (!$enabled) {
            Log::info("Email notification skipped: smtp_notifications_enabled is false.");
            return null;
        }

        // 2. Resolve destination email
        $recipient = $overrideRecipient ?: Setting::get('smtp_recipient_email', env('ADMIN_NOTIFICATION_EMAIL', ''));
        if (empty($recipient)) {
            $adminUser = \App\Models\User::where('role', 'super_admin')->first();
            $recipient = $adminUser ? $adminUser->email : env('MAIL_FROM_ADDRESS', 'admin@krishnacottages.com');
        }

        $host = strtolower(trim(Setting::get('smtp_host', env('MAIL_HOST', ''))));
        $password = trim(Setting::get('smtp_password', env('MAIL_PASSWORD', '')));
        $fromAddress = Setting::get('smtp_from_address', env('MAIL_FROM_ADDRESS', 'noreply@krishnacottages.com'));
        $fromName = Setting::get('smtp_from_name', env('MAIL_FROM_NAME', 'Krishna Cottages'));

        $status = 'pending';
        $failureReason = null;

        // 3. Dispatch via appropriate driver
        if (!empty($recipient) && !empty($password)) {
            // A. Resend REST API
            if (str_starts_with($password, 're_') || str_contains($host, 'resend')) {
                $res = $this->sendViaResendApi($password, $recipient, $subject, $htmlBody, $fromAddress, $fromName);
                $status = $res['status'];
                $failureReason = $res['error'];
            }
            // B. Brevo REST API (HTTPS port 443)
            elseif ($host === 'api.brevo.com' || str_starts_with($password, 'xkeysib-') || (str_contains($host, 'brevo') && (int)Setting::get('smtp_port') === 443)) {
                $res = $this->sendViaBrevoApi($password, $recipient, $subject, $htmlBody, $fromAddress, $fromName);
                $status = $res['status'];
                $failureReason = $res['error'];
            }
            // C. Standard SMTP (with Brevo fallback if port is blocked)
            else {
                $configuredFrom = $this->configureSmtp();
                if ($configuredFrom) {
                    try {
                        Mail::html($htmlBody, function ($message) use ($recipient, $subject, $fromAddress, $fromName) {
                            $message->to($recipient)->subject($subject);
                            if ($fromAddress) {
                                $message->from($fromAddress, $fromName);
                            }
                        });
                        $status = 'delivered';
                    } catch (\Throwable $e) {
                        Log::warning("SMTP Dispatch failed: " . $e->getMessage());
                        if (str_contains($host, 'brevo') || str_starts_with($password, 'xsmtpsib-')) {
                            Log::info("Attempting Brevo HTTPS API fallback...");
                            $res = $this->sendViaBrevoApi($password, $recipient, $subject, $htmlBody, $fromAddress, $fromName);
                            $status = $res['status'];
                            $failureReason = $res['error'];
                        } else {
                            $status = 'failed';
                            $failureReason = $e->getMessage();
                        }
                    }
                } else {
                    $status = 'pending';
                    $failureReason = 'SMTP credentials not configured.';
                }
            }
        } else {
            $status = 'pending';
            $failureReason = 'Recipient or password not configured.';
        }

        // 4. Record in notification_logs table (channel = 'email')
        try {
            return NotificationLog::create([
                'event' => $event,
                'channel' => 'email',
                'recipient' => $recipient ?: 'Unconfigured Admin Email',
                'branch_id' => $branchId,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'subject' => $subject,
                'message_body' => strip_tags($htmlBody),
                'status' => $status,
                'failure_reason' => $failureReason,
                'sent_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to log email notification to notification_logs: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Configure Laravel's SMTP mailer dynamically at runtime from settings.
     */
    protected function configureSmtp(?array $overrideConfig = null): ?string
    {
        $host = $overrideConfig['smtp_host'] ?? Setting::get('smtp_host', env('MAIL_HOST', ''));
        $port = $overrideConfig['smtp_port'] ?? Setting::get('smtp_port', env('MAIL_PORT', 587));
        $encryption = $overrideConfig['smtp_encryption'] ?? Setting::get('smtp_encryption', env('MAIL_ENCRYPTION', 'tls'));
        $username = $overrideConfig['smtp_username'] ?? Setting::get('smtp_username', env('MAIL_USERNAME', ''));
        $password = $overrideConfig['smtp_password'] ?? Setting::get('smtp_password', env('MAIL_PASSWORD', ''));
        $fromAddress = $overrideConfig['smtp_from_address'] ?? Setting::get('smtp_from_address', env('MAIL_FROM_ADDRESS', 'noreply@krishnacottages.com'));
        $fromName = $overrideConfig['smtp_from_name'] ?? Setting::get('smtp_from_name', env('MAIL_FROM_NAME', 'Krishna Cottages'));

        if (empty($host) || empty($username) || empty($password)) {
            return null;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => trim($host),
            'mail.mailers.smtp.port' => (int) $port,
            'mail.mailers.smtp.encryption' => ($encryption === 'none' || empty($encryption)) ? null : trim($encryption),
            'mail.mailers.smtp.username' => trim($username),
            'mail.mailers.smtp.password' => trim($password),
            'mail.mailers.smtp.timeout' => 12,
            'mail.from.address' => trim($fromAddress),
            'mail.from.name' => trim($fromName),
        ]);

        try {
            Mail::purge('smtp');
        } catch (\Throwable $e) {}

        return $fromAddress;
    }

    /**
     * Build responsive HTML email template with Krishna Cottages branding.
     */
    protected function buildHtmlTemplate(
        string $title,
        string $badge,
        string $badgeColor,
        string $lead,
        array $rows,
        string $actionUrl,
        string $actionText
    ): string {
        $rowsHtml = '';
        foreach ($rows as $label => $val) {
            $rowsHtml .= "
                <tr>
                    <td style=\"padding: 10px 14px; font-weight: 600; color: #4b5563; font-size: 13px; border-bottom: 1px solid #f3f4f6; width: 35%; vertical-align: top;\">{$label}</td>
                    <td style=\"padding: 10px 14px; color: #111827; font-size: 13px; border-bottom: 1px solid #f3f4f6; vertical-align: top;\">{$val}</td>
                </tr>";
        }

        $now = Carbon::now()->format('d M Y, h:i A');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$title}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f7f6f2; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f7f6f2; padding: 30px 15px;">
  <tr>
    <td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <!-- Header -->
        <tr>
          <td style="background-color: #083F34; padding: 24px 30px; text-align: left;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <span style="font-family: Georgia, serif; font-size: 20px; font-weight: bold; color: #FAF7F0; letter-spacing: 0.5px;">KRISHNA COTTAGES</span>
                  <div style="font-size: 11px; color: #C9A86A; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px;">Resorts & Botanical Living</div>
                </td>
                <td align="right">
                  <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; color: #ffffff; background-color: {$badgeColor}; letter-spacing: 0.8px; text-transform: uppercase;">{$badge}</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Main Body -->
        <tr>
          <td style="padding: 28px 30px;">
            <h2 style="margin: 0 0 8px 0; font-size: 18px; color: #083F34; font-weight: 700;">{$title}</h2>
            <p style="margin: 0 0 20px 0; font-size: 13px; color: #6b7280; line-height: 1.5;">{$lead}</p>

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #fafaf9; border-radius: 8px; border: 1px solid #e7e5e4; margin-bottom: 24px; border-collapse: collapse;">
              {$rowsHtml}
            </table>

            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td align="center">
                  <a href="{$actionUrl}" style="display: inline-block; background-color: #083F34; color: #FAF7F0; text-decoration: none; font-size: 13px; font-weight: bold; padding: 12px 26px; border-radius: 8px; box-shadow: 0 2px 6px rgba(8,63,52,0.25);">{$actionText} &rarr;</a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background-color: #f9fafb; padding: 18px 30px; text-align: center; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af;">
            Krishna Cottages Automated Operational Gateway &bull; Sent at {$now}<br>
            Please do not reply directly to this notification email.
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
HTML;
    }
}
