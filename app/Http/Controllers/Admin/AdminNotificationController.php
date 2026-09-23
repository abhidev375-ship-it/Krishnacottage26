<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\FacilityBooking;
use App\Models\FoodOrder;
use App\Models\FoodReview;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Setting;
use App\Models\SpiceOrder;
use App\Models\StayExtensionRequest;
use App\Models\TaxiRequest;
use App\Services\EmailNotificationService;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Aggregated live notification feed for the Admin topbar dropdown and background poller.
     */
    public function getNotificationsFeed(Request $request): JsonResponse
    {
        $branchId = $request->query('branch_id');
        $branchFilter = ($branchId && $branchId !== 'all' && is_numeric($branchId)) ? (int) $branchId : null;

        $lastReadAt = session('admin_last_read_notifications_at');
        $lastReadCarbon = $lastReadAt ? Carbon::parse($lastReadAt) : null;

        $feed = collect();

        // 1. Room Reservations (Created in last 48 hours or unconfirmed inquiries)
        $resQuery = Reservation::with(['branch', 'guest', 'roomType', 'room'])
            ->where(function ($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(2))
                  ->orWhere('status', 'inquiry');
            });
        if ($branchFilter) {
            $resQuery->where('branch_id', $branchFilter);
        }
        $reservations = $resQuery->latest('id')->take(10)->get();

        foreach ($reservations as $r) {
            $guestName = $r->guest ? $r->guest->full_name : 'Guest';
            $roomName = $r->roomType ? $r->roomType->name : 'Cottage Villa';
            $isUnread = $lastReadCarbon ? $r->created_at->gt($lastReadCarbon) : true;

            $feed->push([
                'id' => "res_{$r->id}",
                'type' => 'reservation',
                'category' => 'stays',
                'icon' => 'calendar-check',
                'color' => 'emerald',
                'title' => "Booking #{$r->booking_code} · " . ucfirst($r->status),
                'subtitle' => "{$guestName} · {$roomName} (" . Carbon::parse($r->check_in_date)->format('d M') . " - " . Carbon::parse($r->check_out_date)->format('d M') . ")",
                'meta' => "₹" . number_format($r->total_amount, 2) . " · " . strtoupper($r->payment_status ?? 'PENDING'),
                'timestamp' => $r->created_at->toIso8601String(),
                'time_ago' => $r->created_at->diffForHumans(),
                'action_section' => 'reservations',
                'action_type' => 'open_reservation',
                'action_id' => $r->id,
                'unread' => $isUnread,
            ]);
        }

        // 2. Enquiries & Guest Chat Messages
        $enqQuery = Enquiry::with(['branch', 'latestMessage'])
            ->where('has_unread_messages', true);
        if ($branchFilter) {
            $enqQuery->where(function ($q) use ($branchFilter) {
                $q->where('branch_id', $branchFilter)
                  ->orWhereNull('branch_id');
            });
        }
        $enquiries = $enqQuery->latest('last_message_at')->take(10)->get();

        foreach ($enquiries as $e) {
            $snippet = $e->latestMessage ? mb_strimwidth(strip_tags($e->latestMessage->message), 0, 60, '...') : ($e->subject ?: 'New customer enquiry');
            $topic = ucfirst(str_replace('_', ' ', $e->topic ?? 'general'));

            $feed->push([
                'id' => "enq_{$e->id}",
                'type' => 'enquiry',
                'category' => 'messages',
                'icon' => 'message-square',
                'color' => 'amber',
                'title' => "Enquiry #{$e->ticket_number} [{$topic}]",
                'subtitle' => "{$e->customer_name}: \"{$snippet}\"",
                'meta' => ($e->branch ? $e->branch->code : 'Central') . " · " . ucfirst($e->priority ?? 'medium'),
                'timestamp' => ($e->last_message_at ?? $e->updated_at)->toIso8601String(),
                'time_ago' => ($e->last_message_at ?? $e->updated_at)->diffForHumans(),
                'action_section' => 'messages',
                'action_type' => 'open_enquiry',
                'action_id' => $e->id,
                'unread' => true,
            ]);
        }

        // 3. Food / Kitchen Orders (Active New Orders)
        $foodQuery = FoodOrder::with(['branch', 'items.menuItem'])
            ->where('status', 'new');
        if ($branchFilter) {
            $foodQuery->where('branch_id', $branchFilter);
        }
        $foodOrders = $foodQuery->latest('ordered_at')->take(10)->get();

        foreach ($foodOrders as $f) {
            $loc = $f->room_number ? "Villa {$f->room_number}" : ($f->table_number ? "Table {$f->table_number}" : 'Restaurant');
            $itemsCount = $f->items ? $f->items->sum('quantity') : 1;

            $feed->push([
                'id' => "food_{$f->id}",
                'type' => 'food_order',
                'category' => 'dining',
                'icon' => 'chef-hat',
                'color' => 'orange',
                'title' => "Kitchen Ticket #{$f->order_number}",
                'subtitle' => "{$f->customer_name} · {$loc} ({$itemsCount} item" . ($itemsCount > 1 ? 's' : '') . ")",
                'meta' => "₹" . number_format($f->total_amount, 2) . " · " . strtoupper($f->order_type ?? 'ROOM SERVICE'),
                'timestamp' => ($f->ordered_at ?? $f->created_at)->toIso8601String(),
                'time_ago' => ($f->ordered_at ?? $f->created_at)->diffForHumans(),
                'action_section' => 'food-orders',
                'action_type' => 'open_food_orders',
                'action_id' => $f->id,
                'unread' => true,
            ]);
        }

        // 4. Stay Extension Requests (Pending Manager Approval)
        $extQuery = StayExtensionRequest::with(['reservation.guest', 'reservation.room', 'reservation.branch'])
            ->where('status', 'pending');
        if ($branchFilter) {
            $extQuery->whereHas('reservation', function ($q) use ($branchFilter) {
                $q->where('branch_id', $branchFilter);
            });
        }
        $extensions = $extQuery->latest('created_at')->take(5)->get();

        foreach ($extensions as $ext) {
            $res = $ext->reservation;
            $guestName = ($res && $res->guest) ? $res->guest->full_name : 'In-House Guest';
            $villa = ($res && $res->room) ? "Villa {$res->room->room_number}" : 'Cottage Suite';

            $feed->push([
                'id' => "ext_{$ext->id}",
                'type' => 'extension',
                'category' => 'stays',
                'icon' => 'clock',
                'color' => 'blue',
                'title' => "Extension Request (+{$ext->extra_nights} Nights)",
                'subtitle' => "{$guestName} ({$villa}) · Until " . Carbon::parse($ext->requested_checkout_date)->format('d M Y'),
                'meta' => "Est. ₹" . number_format($ext->standard_amount, 2) . " · Awaiting Manager Offer",
                'timestamp' => $ext->created_at->toIso8601String(),
                'time_ago' => $ext->created_at->diffForHumans(),
                'action_section' => 'in-house',
                'action_type' => 'open_in_house',
                'action_id' => $ext->id,
                'unread' => true,
            ]);
        }

        // 5. Facility / Experience Bookings (Pending Time Allocation)
        $facQuery = FacilityBooking::with(['facility', 'guest', 'reservation.room', 'branch'])
            ->where('status', 'pending');
        if ($branchFilter) {
            $facQuery->where('branch_id', $branchFilter);
        }
        $facilities = $facQuery->latest('created_at')->take(5)->get();

        foreach ($facilities as $fb) {
            $facName = $fb->facility ? $fb->facility->name : 'Resort Experience';
            $guestName = $fb->guest ? $fb->guest->full_name : 'Guest';
            $villa = ($fb->reservation && $fb->reservation->room) ? " (Villa {$fb->reservation->room->room_number})" : '';

            $feed->push([
                'id' => "fac_{$fb->id}",
                'type' => 'facility',
                'category' => 'services',
                'icon' => 'sparkles',
                'color' => 'teal',
                'title' => "Experience: {$facName}",
                'subtitle' => "{$guestName}{$villa} · " . Carbon::parse($fb->booking_date)->format('d M') . " ({$fb->guests_count} pax)",
                'meta' => "₹" . number_format($fb->total_amount, 2) . " · Concierge Action Required",
                'timestamp' => $fb->created_at->toIso8601String(),
                'time_ago' => $fb->created_at->diffForHumans(),
                'action_section' => 'in-house',
                'action_type' => 'open_in_house',
                'action_id' => $fb->id,
                'unread' => true,
            ]);
        }

        // 6. Taxi / Excursion Cab Requests (Pending Dispatch)
        $taxiQuery = TaxiRequest::with(['guest', 'reservation.room', 'branch', 'selected_locations'])
            ->where('status', 'pending');
        if ($branchFilter) {
            $taxiQuery->where('branch_id', $branchFilter);
        }
        $taxis = $taxiQuery->latest('created_at')->take(5)->get();

        foreach ($taxis as $tx) {
            $guestName = $tx->guest ? $tx->guest->full_name : 'Guest';
            $villa = ($tx->reservation && $tx->reservation->room) ? "Villa {$tx->reservation->room->room_number}" : 'Resort Guest';
            $stops = $tx->selected_locations ? $tx->selected_locations->pluck('name')->implode(', ') : 'Custom Tour';

            $feed->push([
                'id' => "taxi_{$tx->id}",
                'type' => 'taxi',
                'category' => 'services',
                'icon' => 'car',
                'color' => 'indigo',
                'title' => "Cab Excursion #{$tx->booking_reference}",
                'subtitle' => "{$guestName} ({$villa}) · " . Carbon::parse($tx->pickup_date)->format('d M') . " at {$tx->pickup_time}",
                'meta' => "{$tx->passengers_count} Pax · " . mb_strimwidth($stops, 0, 35, '...'),
                'timestamp' => $tx->created_at->toIso8601String(),
                'time_ago' => $tx->created_at->diffForHumans(),
                'action_section' => 'in-house',
                'action_type' => 'open_in_house',
                'action_id' => $tx->id,
                'unread' => true,
            ]);
        }

        // 7. Spice Store Orders (Processing / Paid)
        $spiceQuery = SpiceOrder::with(['items.product'])
            ->whereIn('status', ['processing', 'paid'])
            ->where('created_at', '>=', Carbon::now()->subDays(7));
        $spiceOrders = $spiceQuery->latest('created_at')->take(5)->get();

        foreach ($spiceOrders as $sp) {
            $itemCount = $sp->items ? $sp->items->sum('quantity') : 1;
            $mode = strtoupper($sp->delivery_mode ?? 'COURIER');
            $loc = ($sp->delivery_mode === 'villa' && $sp->room_number) ? "Villa {$sp->room_number}" : $sp->shipping_city;

            $feed->push([
                'id' => "spice_{$sp->id}",
                'type' => 'spice_order',
                'category' => 'shop',
                'icon' => 'package',
                'color' => 'emerald',
                'title' => "Spice Order #{$sp->order_number}",
                'subtitle' => "{$sp->customer_name} · {$mode} ({$loc})",
                'meta' => "₹" . number_format($sp->total_amount, 2) . " · {$itemCount} pack(s)",
                'timestamp' => $sp->created_at->toIso8601String(),
                'time_ago' => $sp->created_at->diffForHumans(),
                'action_section' => 'spice-orders',
                'action_type' => 'open_spice_orders',
                'action_id' => $sp->id,
                'unread' => true,
            ]);
        }

        // 8. Spice Return / Refund Requests
        $returnQuery = SpiceOrder::where('refund_status', 'requested');
        $returns = $returnQuery->latest('updated_at')->take(5)->get();

        foreach ($returns as $sr) {
            $cashback = number_format($sr->refund_amount ?? 0, 2);

            $feed->push([
                'id' => "spice_ret_{$sr->id}",
                'type' => 'spice_return',
                'category' => 'shop',
                'icon' => 'rotate-ccw',
                'color' => 'rose',
                'title' => "Spice Return #{$sr->order_number}",
                'subtitle' => "{$sr->customer_name} · Eligible Cashback: ₹{$cashback}",
                'meta' => mb_strimwidth($sr->cancellation_reason ?? 'Return requested', 0, 45, '...'),
                'timestamp' => $sr->updated_at->toIso8601String(),
                'time_ago' => $sr->updated_at->diffForHumans(),
                'action_section' => 'spices',
                'action_type' => 'open_spice_returns',
                'action_id' => $sr->id,
                'unread' => true,
            ]);
        }

        // 9. Verified Guest Reviews Awaiting Moderation
        $roomReviews = Review::where('status', 'pending')->latest('created_at')->take(5)->get();
        foreach ($roomReviews as $rr) {
            $feed->push([
                'id' => "rev_room_{$rr->id}",
                'type' => 'review',
                'category' => 'reviews',
                'icon' => 'star',
                'color' => 'amber',
                'title' => "Pending Stay Review ({$rr->rating}★)",
                'subtitle' => "{$rr->stay_summary}: \"" . mb_strimwidth($rr->comment, 0, 50, '...') . "\"",
                'meta' => "Verified Stay Moderation",
                'timestamp' => $rr->created_at->toIso8601String(),
                'time_ago' => $rr->created_at->diffForHumans(),
                'action_section' => 'reviews',
                'action_type' => 'open_reviews',
                'action_id' => $rr->id,
                'unread' => true,
            ]);
        }

        $foodReviews = FoodReview::where('status', 'pending')->latest('created_at')->take(5)->get();
        foreach ($foodReviews as $fr) {
            $feed->push([
                'id' => "rev_food_{$fr->id}",
                'type' => 'review',
                'category' => 'reviews',
                'icon' => 'star',
                'color' => 'amber',
                'title' => "Pending Dining Review ({$fr->rating}★)",
                'subtitle' => "{$fr->customer_name}: \"" . mb_strimwidth($fr->comment ?? 'Great meal experience', 0, 50, '...') . "\"",
                'meta' => "Kitchen Feedback Moderation",
                'timestamp' => $fr->created_at->toIso8601String(),
                'time_ago' => $fr->created_at->diffForHumans(),
                'action_section' => 'reviews',
                'action_type' => 'open_reviews',
                'action_id' => $fr->id,
                'unread' => true,
            ]);
        }

        // Sort all items chronologically by timestamp DESC
        $sortedFeed = $feed->sortByDesc('timestamp')->values();

        $unreadCount = $sortedFeed->where('unread', true)->count();

        $countsByCategory = [
            'all' => $sortedFeed->count(),
            'stays' => $sortedFeed->where('category', 'stays')->count(),
            'messages' => $sortedFeed->where('category', 'messages')->count(),
            'dining' => $sortedFeed->where('category', 'dining')->count(),
            'services' => $sortedFeed->where('category', 'services')->count(),
            'shop' => $sortedFeed->where('category', 'shop')->count(),
            'reviews' => $sortedFeed->where('category', 'reviews')->count(),
        ];

        return response()->json([
            'success' => true,
            'total_unread' => $unreadCount,
            'counts_by_category' => $countsByCategory,
            'notifications' => $sortedFeed,
            'polled_at' => Carbon::now()->toIso8601String(),
        ]);
    }

    /**
     * Acknowledge and mark all current notifications as read.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        session(['admin_last_read_notifications_at' => Carbon::now()->toIso8601String()]);

        return response()->json([
            'success' => true,
            'message' => 'All operational notifications acknowledged.',
        ]);
    }

    /**
     * Save SMTP Email notification settings.
     */
     public function saveEmailSettings(Request $request): JsonResponse
     {
         $validated = $request->validate([
             'smtp_notifications_enabled' => 'nullable|boolean',
             'smtp_host' => 'nullable|string|max:190',
             'smtp_port' => 'nullable|integer',
             'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
             'smtp_username' => 'nullable|string|max:190',
             'smtp_password' => 'nullable|string|max:190',
             'smtp_from_address' => 'nullable|string|email|max:190',
             'smtp_from_name' => 'nullable|string|max:120',
             'smtp_recipient_email' => 'nullable|string|email|max:190',
         ]);

         Setting::set('smtp_notifications_enabled', (bool) ($validated['smtp_notifications_enabled'] ?? false), 'system', 'Enable/disable automated SMTP email notifications');
         Setting::set('smtp_host', trim($validated['smtp_host'] ?? ''), 'system', 'SMTP Host address');
         Setting::set('smtp_port', (int) ($validated['smtp_port'] ?? 587), 'system', 'SMTP Port');
         Setting::set('smtp_encryption', trim($validated['smtp_encryption'] ?? 'tls'), 'system', 'SMTP Encryption (tls/ssl/none)');
         Setting::set('smtp_username', trim($validated['smtp_username'] ?? ''), 'system', 'SMTP Username');
         if (!empty($validated['smtp_password'])) {
             Setting::set('smtp_password', trim($validated['smtp_password']), 'system', 'SMTP Password');
         }
         Setting::set('smtp_from_address', trim($validated['smtp_from_address'] ?? ''), 'system', 'SMTP Outbound From Address');
         Setting::set('smtp_from_name', trim($validated['smtp_from_name'] ?? 'Krishna Cottages'), 'system', 'SMTP Outbound Sender Name');
         Setting::set('smtp_recipient_email', trim($validated['smtp_recipient_email'] ?? ''), 'system', 'Admin Recipient Email for Operational Alerts');

         return response()->json([
             'success' => true,
             'message' => 'SMTP Email configuration saved successfully.',
         ]);
     }

    /**
     * Test SMTP Email sending.
     */
    public function testEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string|max:190',
            'smtp_port' => 'nullable|integer',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_username' => 'nullable|string|max:190',
            'smtp_password' => 'nullable|string|max:190',
            'smtp_from_address' => 'nullable|string|email|max:190',
            'smtp_from_name' => 'nullable|string|max:120',
            'recipient_email' => 'required|string|email|max:190',
        ]);

        if (empty($validated['smtp_password'])) {
            $validated['smtp_password'] = Setting::get('smtp_password', env('MAIL_PASSWORD', ''));
        }

        $service = app(EmailNotificationService::class);
        $result = $service->sendTestEmail($validated, $validated['recipient_email']);

        return response()->json($result);
    }

    /**
     * Save Telegram Bot notification settings.
     */
    public function saveTelegramSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'telegram_notifications_enabled' => 'nullable|boolean',
            'telegram_bot_token' => 'nullable|string|max:190',
            'telegram_chat_id' => 'nullable|string|max:100',
        ]);

        Setting::set('telegram_notifications_enabled', (bool) ($validated['telegram_notifications_enabled'] ?? false), 'system', 'Enable/disable automated Telegram Bot notifications');
        if (!empty($validated['telegram_bot_token'])) {
            Setting::set('telegram_bot_token', trim($validated['telegram_bot_token']), 'system', 'Telegram Bot Token');
        }
        Setting::set('telegram_chat_id', trim($validated['telegram_chat_id'] ?? ''), 'system', 'Admin Telegram Chat ID / Channel ID');

        return response()->json([
            'success' => true,
            'message' => 'Telegram Bot configuration saved successfully.',
        ]);
    }

    /**
     * Test Telegram Bot message dispatch.
     */
    public function testTelegram(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bot_token' => 'nullable|string|max:190',
            'chat_id' => 'required|string|max:100',
        ]);

        $botToken = !empty($validated['bot_token']) ? trim($validated['bot_token']) : Setting::get('telegram_bot_token', env('TELEGRAM_BOT_TOKEN', ''));

        if (empty($botToken)) {
            return response()->json([
                'success' => false,
                'status' => 'failed',
                'error' => 'Telegram Bot Token is required to test connection.',
            ]);
        }

        $service = app(TelegramNotificationService::class);
        $result = $service->sendTestMessage($botToken, trim($validated['chat_id']));

        return response()->json($result);
    }
}
