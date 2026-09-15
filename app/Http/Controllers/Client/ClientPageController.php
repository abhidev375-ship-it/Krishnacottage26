<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\Facility;
use App\Models\GalleryAlbum;
use App\Models\Guest;
use App\Models\NearbyLocation;
use App\Models\NotificationLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientPageController extends Controller
{
    public function facilities(Request $request)
    {
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        $selectedBranchId = $request->query('branch_id');
        $selectedCategory = $request->query('category');

        $query = Facility::with('branch')->where('is_published', true);

        if ($selectedBranchId) {
            $query->where(function ($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId)->orWhereNull('branch_id');
            });
        }

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        $facilities = $query->orderBy('sort_order')->orderBy('name')->get();

        return view('client.facilities.index', compact('facilities', 'branches', 'selectedBranchId', 'selectedCategory'));
    }

    public function gallery(Request $request)
    {
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        $selectedBranchId = $request->query('branch_id');
        $selectedCategory = $request->query('category');

        $query = GalleryAlbum::with(['images' => function ($q) {
            $q->orderBy('sort_order');
        }, 'branch'])->where('is_published', true);

        if ($selectedBranchId === 'resort' || $selectedBranchId === 'none') {
            $query->whereNull('branch_id');
        } elseif ($selectedBranchId && $selectedBranchId !== 'all') {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        $albums = $query->orderBy('sort_order')->orderBy('id', 'desc')->get();

        return view('client.gallery.index', compact('albums', 'branches', 'selectedBranchId', 'selectedCategory'));
    }

    public function nearby(Request $request)
    {
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        $selectedBranchId = $request->query('branch_id');
        $selectedCategory = $request->query('category');

        $query = NearbyLocation::with('branch')->where('is_available', true);

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        $locations = $query->orderBy('sort_order')->get();

        return view('client.nearby.index', compact('locations', 'branches', 'selectedBranchId', 'selectedCategory'));
    }

    public function contact()
    {
        $branches = Branch::where('status', 'active')->orderBy('sort_order')->get();
        $contactContent = Setting::get('contact_content', []);

        $centralPhone = Setting::get('resort_phone', $contactContent['central_phone'] ?? '+91 94471 22334');
        $centralEmail = Setting::get('resort_email', $contactContent['central_email'] ?? 'concierge@krishnaresorts.com');
        $centralAddress = Setting::get('resort_address', $contactContent['central_address'] ?? 'Tea Garden Estate, Rajakkad, Idukki District, Kerala - 685566');
        $frontdeskHours = Setting::get('frontdesk_hours', $contactContent['frontdesk_hours'] ?? 'Open 24 Hours · 7 Days a Week');

        return view('client.contact', compact('branches', 'contactContent', 'centralPhone', 'centralEmail', 'centralAddress', 'frontdeskHours'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'branch_id' => 'nullable|exists:branches,id',
            'topic' => 'nullable|string|in:booking_related,dining,spices,events,general',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        $userId = Auth::id();
        $guestId = null;
        if ($userId) {
            $guestId = Guest::where('user_id', $userId)->value('id');
        }
        if (!$guestId) {
            $guestId = Guest::where('email', $validated['email'])
                ->orWhere(function ($q) use ($validated) {
                    if (!empty($validated['phone'])) {
                        $q->where('phone', $validated['phone']);
                    }
                })->value('id');
        }

        $now = Carbon::now();
        $ticketNumber = 'ENQ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $enquiry = Enquiry::create([
            'ticket_number' => $ticketNumber,
            'client_session_id' => session()->getId(),
            'branch_id' => $validated['branch_id'] ?? null,
            'guest_id' => $guestId,
            'user_id' => $userId,
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'] ?? null,
            'topic' => $validated['topic'] ?? 'general',
            'subject' => $validated['subject'],
            'status' => 'new',
            'priority' => 'medium',
            'has_unread_messages' => true,
            'last_message_at' => $now,
            'created_at' => $now,
        ]);

        EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'customer',
            'user_id' => $userId,
            'message' => $validated['message'],
            'created_at' => $now,
        ]);

        // Create notification for admin desk
        NotificationLog::create([
            'event' => 'enquiry.received',
            'channel' => 'email',
            'recipient' => 'concierge_desk',
            'branch_id' => $enquiry->branch_id,
            'reference_type' => 'Enquiry',
            'reference_id' => $enquiry->id,
            'subject' => "New Web Enquiry: {$ticketNumber}",
            'message_body' => "{$validated['name']} ({$validated['email']}) submitted inquiry '{$validated['subject']}': " . Str::limit($validated['message'], 160),
            'status' => 'delivered',
            'sent_at' => $now,
        ]);

        // Dispatch real-time CallMeBot WhatsApp alert to admin
        try {
            app(\App\Services\WhatsAppNotificationService::class)->sendEnquiryAlert($enquiry, $validated['message']);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("WhatsApp Enquiry Alert failed: " . $e->getMessage());
        }

        AuditLog::create([
            'user_id' => $userId ?? 1,
            'user_name' => $validated['name'] . ($userId ? ' (Registered Guest)' : ' (Web Guest)'),
            'role' => 'customer',
            'branch_id' => $enquiry->branch_id,
            'action' => 'web_enquiry',
            'entity_type' => 'Enquiry',
            'entity_id' => $enquiry->id,
            'previous_values' => [],
            'new_values' => ['ticket' => $ticketNumber, 'subject' => $validated['subject'], 'topic' => $enquiry->topic],
            'ip_address' => $request->ip(),
            'created_at' => $now,
        ]);

        $successMessage = "Thank you, {$validated['name']}. Your inquiry (#{$ticketNumber}) has been submitted. Our concierge team typically responds within 15 minutes.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'enquiry' => [
                    'id' => $enquiry->id,
                    'ticket_number' => $ticketNumber,
                    'customer_name' => $enquiry->customer_name,
                    'customer_email' => $enquiry->customer_email,
                    'subject' => $enquiry->subject,
                    'topic' => $enquiry->topic,
                    'branch_name' => $enquiry->branch ? $enquiry->branch->name : 'General Concierge',
                    'created_at' => $now->format('d M Y, H:i'),
                ]
            ]);
        }

        return back()
            ->with('success', $successMessage)
            ->with('ticket_number', $ticketNumber)
            ->with('enquiry_id', $enquiry->id);
    }
}
