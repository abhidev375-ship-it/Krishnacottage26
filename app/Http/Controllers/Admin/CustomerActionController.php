<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\ChatTemplate;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\MenuItem;
use App\Models\NearbyLocation;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\RoomType;
use App\Models\SpiceProduct;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerActionController extends Controller
{
    /**
     * Claim and lock a chat conversation exclusively for the current staff user.
     */
    public function claimChat(Request $request, int $enquiryId): JsonResponse
    {
        $enquiry = Enquiry::with('lockedBy')->findOrFail($enquiryId);
        $user = Auth::user();

        if ($enquiry->isLocked() && !$enquiry->isLockedBy($user->id) && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => "This chat is already locked exclusively by {$enquiry->lockedBy->name}.",
            ], 403);
        }

        $enquiry->update([
            'locked_by' => $user->id,
            'locked_at' => Carbon::now(),
            'assigned_to' => $user->id,
            'status' => in_array($enquiry->status, ['new']) ? 'in_progress' : $enquiry->status,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role,
            'branch_id' => $enquiry->branch_id,
            'action' => 'claim_chat',
            'entity_type' => 'Enquiry',
            'entity_id' => $enquiry->id,
            'new_values' => ['locked_by' => $user->id, 'locked_at' => Carbon::now()],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "You have committed to this chat. It is now locked to your account.",
            'locked_by' => $user->name,
            'locked_by_id' => $user->id,
        ]);
    }

    /**
     * Relieve / release the chat lock so other admins or managers can handle it.
     */
    public function relieveChat(Request $request, int $enquiryId): JsonResponse
    {
        $enquiry = Enquiry::with('lockedBy')->findOrFail($enquiryId);
        $user = Auth::user();

        if ($enquiry->isLocked() && !$enquiry->isLockedBy($user->id) && !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => "Only {$enquiry->lockedBy->name} (or a Super Admin) can relieve this chat.",
            ], 403);
        }

        $prevLocker = $enquiry->lockedBy ? $enquiry->lockedBy->name : 'Staff';
        $enquiry->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role,
            'branch_id' => $enquiry->branch_id,
            'action' => 'relieve_chat',
            'entity_type' => 'Enquiry',
            'entity_id' => $enquiry->id,
            'new_values' => ['relieved_by' => $user->id, 'previous_locked_by' => $prevLocker],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Chat lock released. Other admins and managers can now claim it.",
        ]);
    }

    /**
     * Super Admin emergency override to unlock a chat.
     */
    public function forceUnlockChat(Request $request, int $enquiryId): JsonResponse
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => "Only a Super Admin can force-unlock conversations.",
            ], 403);
        }

        $enquiry = Enquiry::findOrFail($enquiryId);
        $enquiry->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role,
            'branch_id' => $enquiry->branch_id,
            'action' => 'force_unlock_chat',
            'entity_type' => 'Enquiry',
            'entity_id' => $enquiry->id,
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Emergency unlock executed. Chat is now open.",
        ]);
    }

    /**
     * Send staff reply, internal note, or rich interactive action card.
     */
    public function sendMessage(Request $request, int $enquiryId): JsonResponse
    {
        $enquiry = Enquiry::with('lockedBy')->findOrFail($enquiryId);
        $user = Auth::user();

        // Enforce lock verification
        if (!$enquiry->canStaffAccess($user)) {
            return response()->json([
                'success' => false,
                'message' => "Cannot send message: This conversation is locked exclusively by {$enquiry->lockedBy->name}.",
            ], 403);
        }

        $request->validate([
            'message' => 'nullable|string',
            'card_type' => 'nullable|string',
            'entity_id' => 'nullable|integer',
            'card_payload' => 'nullable|array',
            'cards' => 'nullable|array',
            'is_internal_note' => 'nullable|boolean',
            'target_room' => 'nullable|string|max:60',
        ]);

        $isInternal = $request->boolean('is_internal_note', false);
        $textMessage = trim($request->input('message') ?? '');
        $targetRoom = !empty($request->input('target_room')) && $request->input('target_room') !== 'all' ? trim($request->input('target_room')) : null;

        // Support multiple cards in $request->cards OR single card in card_type / entity_id / card_payload
        $allCards = [];
        if ($request->has('cards') && is_array($request->input('cards'))) {
            foreach ($request->input('cards') as $item) {
                if (!empty($item['card_payload']) && is_array($item['card_payload'])) {
                    $allCards[] = $item['card_payload'];
                } elseif (!empty($item['card_type']) && !empty($item['entity_id'])) {
                    $built = $this->buildCardPayload($item['card_type'], (int) $item['entity_id']);
                    if ($built) {
                        $allCards[] = $built;
                    }
                }
            }
        } elseif ($request->input('card_type') && $request->input('entity_id')) {
            $built = $this->buildCardPayload($request->input('card_type'), (int) $request->input('entity_id'));
            if ($built) {
                $allCards[] = $built;
            }
        } elseif ($request->input('card_payload')) {
            $allCards[] = $request->input('card_payload');
        }

        if (empty($textMessage) && empty($allCards)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide either a message or select an action card to send.',
            ], 422);
        }

        $cardType = null;
        $cardPayload = null;

        if (count($allCards) === 1) {
            $cardType = $allCards[0]['card_type'] ?? 'card';
            $cardPayload = array_merge($allCards[0], ['cards' => $allCards]);
        } elseif (count($allCards) > 1) {
            $cardType = 'multi';
            $cardPayload = [
                'cards' => $allCards,
                'card_count' => count($allCards),
                'title' => count($allCards) . ' Interactive Recommendations',
            ];
        }

        $msg = EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'staff',
            'user_id' => $user ? $user->id : 1,
            'message' => $textMessage ?: ($cardPayload['title'] ?? 'Interactive Card'),
            'target_room' => $targetRoom,
            'card_type' => $cardType,
            'card_payload' => $cardPayload,
            'is_internal_note' => $isInternal,
        ]);

        $enquiry->update([
            'last_message_at' => Carbon::now(),
            'has_unread_messages' => false,
            'status' => $isInternal ? $enquiry->status : 'in_progress',
        ]);

        return response()->json([
            'success' => true,
            'message' => $isInternal ? 'Internal staff note added.' : 'Reply sent to guest.',
            'sent_message' => $msg,
            'enquiry' => $enquiry->fresh(['messages.user', 'assignedStaff', 'lockedBy']),
        ]);
    }

    /**
     * Fetch conversation details, messages, customer upcoming stay, and lock info for Admin Chat View.
     */
    public function getChatData(Request $request, int $enquiryId): JsonResponse
    {
        $enquiry = Enquiry::with(['messages.user', 'assignedStaff', 'lockedBy', 'branch'])
            ->findOrFail($enquiryId);
        $user = Auth::user();

        // Check if customer has an active or upcoming stay
        $today = Carbon::today();
        $upcomingReservation = Reservation::with(['branch', 'room', 'roomType'])
            ->where(function ($q) use ($enquiry) {
                if ($enquiry->reservation_id) {
                    $q->where('id', $enquiry->reservation_id);
                }
                if ($enquiry->guest_id) {
                    $q->orWhere('guest_id', $enquiry->guest_id);
                }
                $q->orWhereHas('guest', function ($sub) use ($enquiry) {
                    $sub->where(function ($w) use ($enquiry) {
                        if ($enquiry->customer_email) {
                            $w->orWhere('email', $enquiry->customer_email);
                        }
                        if ($enquiry->customer_phone) {
                            $w->orWhere('phone', $enquiry->customer_phone);
                        }
                        if ($enquiry->user_id) {
                            $w->orWhere('user_id', $enquiry->user_id);
                        }
                    });
                });
            })
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<=', $today->copy()->addDays(7))
            ->where('check_out_date', '>=', $today)
            ->orderByRaw("FIELD(status, 'checked_in', 'confirmed')")
            ->orderBy('check_in_date', 'asc')
            ->first();

        $rooms = [];
        if ($upcomingReservation) {
            if ($upcomingReservation->room) {
                $rooms[] = (string) $upcomingReservation->room->room_number;
            }
            $totalExpected = max(count($rooms), (int) ($upcomingReservation->rooms_count ?? 1));
            if (count($rooms) < $totalExpected) {
                $startNum = !empty($rooms) && is_numeric($rooms[0]) ? (int)$rooms[0] : 101;
                while (count($rooms) < $totalExpected) {
                    $candidate = (string)($startNum + count($rooms));
                    if (!in_array($candidate, $rooms)) {
                        $rooms[] = $candidate;
                    } else {
                        $rooms[] = "Unit " . (count($rooms) + 1);
                    }
                }
            }
        }

        $isLockedByOther = $enquiry->isLocked() && !$enquiry->isLockedBy($user->id) && !$user->isSuperAdmin();

        return response()->json([
            'success' => true,
            'enquiry' => [
                'id' => $enquiry->id,
                'ticket_number' => $enquiry->ticket_number,
                'customer_name' => $enquiry->customer_name,
                'customer_email' => $enquiry->customer_email,
                'customer_phone' => $enquiry->customer_phone,
                'status' => $enquiry->status,
                'priority' => $enquiry->priority,
                'branch_id' => $enquiry->branch_id,
                'branch_name' => $enquiry->branch ? $enquiry->branch->name : 'Resort Central',
                'is_locked' => $enquiry->isLocked(),
                'locked_by' => $enquiry->lockedBy ? $enquiry->lockedBy->name : null,
                'locked_by_id' => $enquiry->locked_by,
                'locked_at' => $enquiry->locked_at ? $enquiry->locked_at->format('d M, H:i') : null,
                'is_locked_by_current_user' => $enquiry->isLockedBy($user->id),
                'is_locked_by_other' => $isLockedByOther,
                'can_staff_access' => $enquiry->canStaffAccess($user),
            ],
            'upcoming_stay' => $upcomingReservation ? [
                'booking_code' => $upcomingReservation->booking_code,
                'reservation_id' => $upcomingReservation->id,
                'branch_id' => $upcomingReservation->branch_id,
                'branch_name' => $upcomingReservation->branch ? $upcomingReservation->branch->name : '',
                'room_type' => $upcomingReservation->roomType ? $upcomingReservation->roomType->name : 'Resort Cottage',
                'check_in' => Carbon::parse($upcomingReservation->check_in_date)->format('d M Y'),
                'check_out' => Carbon::parse($upcomingReservation->check_out_date)->format('d M Y'),
                'status' => $upcomingReservation->status,
                'is_in_house' => $upcomingReservation->status === 'checked_in' || ($upcomingReservation->status === 'confirmed' && Carbon::parse($upcomingReservation->check_in_date)->isToday()),
                'rooms' => $rooms,
                'is_multi_room' => count($rooms) > 1,
            ] : null,
            'messages' => $isLockedByOther ? [] : $enquiry->messages,
        ]);
    }

    /**
     * Return all predefined quick answers, card templates, and entity catalogs for ComboBox.
     */
    public function getTemplates(): JsonResponse
    {
        $templates = ChatTemplate::where('is_active', true)->orderBy('category')->get();

        $branches = Branch::where('status', 'active')
            ->select('id', 'name', 'city', 'tagline', 'hero_image_url')
            ->get();

        $roomTypes = RoomType::where('is_active', true)
            ->with('branch:id,name')
            ->select('id', 'branch_id', 'name', 'slug', 'base_price', 'cover_image_url', 'max_guests')
            ->get();

        $spices = SpiceProduct::where('status', 'active')
            ->select('id', 'name', 'price', 'package_size', 'image_url')
            ->get();

        $dining = MenuItem::where('availability_state', 'in_stock')
            ->select('id', 'name', 'price', 'is_vegetarian', 'image_url')
            ->take(15)
            ->get();

        $nearby = NearbyLocation::select('id', 'name', 'distance_km', 'travel_time', 'image_url')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates,
            'catalogs' => [
                'branches' => $branches,
                'room_types' => $roomTypes,
                'spices' => $spices,
                'dining' => $dining,
                'nearby' => $nearby,
            ],
        ]);
    }

    /**
     * Get recent active threads with unread counts for dynamic admin real-time sync.
     */
    public function getThreads(Request $request): JsonResponse
    {
        $branchId = $request->query('branch_id');
        $enquiries = Enquiry::with(['branch', 'messages', 'lockedBy'])
            ->when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->take(30)
            ->get()
            ->map(function ($enq) {
                $lastMsg = $enq->messages->last();
                return [
                    'id' => $enq->id,
                    'ticket_number' => $enq->ticket_number,
                    'customer_name' => $enq->customer_name,
                    'customer_email' => $enq->customer_email,
                    'customer_phone' => $enq->customer_phone,
                    'subject' => $enq->subject,
                    'topic' => $enq->topic,
                    'status' => $enq->status,
                    'has_unread_messages' => (bool) $enq->has_unread_messages,
                    'branch_name' => $enq->branch ? $enq->branch->name : 'Resort Central',
                    'branch_code' => $enq->branch ? $enq->branch->code : 'ALL',
                    'last_message' => $lastMsg ? \Illuminate\Support\Str::limit($lastMsg->message, 65) : 'No messages yet',
                    'last_time' => $enq->last_message_at ? $enq->last_message_at->format('H:i') : $enq->created_at->format('d M'),
                    'is_locked' => $enq->isLocked(),
                    'is_locked_by_me' => $enq->isLockedBy(auth()->id()),
                    'locked_by_name' => $enq->lockedBy ? $enq->lockedBy->name : null,
                    'messages_count' => $enq->messages->count(),
                ];
            });

        $unreadCount = Enquiry::when($branchId, fn($q) => $q->where(fn($sq) => $sq->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->where('has_unread_messages', true)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $enquiries->count(),
            'unread_count' => $unreadCount,
            'threads' => $enquiries,
        ]);
    }

    /**
     * Store a new custom quick action template created manually by an admin.
     */
    public function storeTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'category' => 'required|string|max:50',
            'type' => 'required|in:text,branch,room,spice,dining,nearby',
            'message' => 'nullable|string',
            'entity_id' => 'nullable|integer',
            'card_payload' => 'nullable|array',
        ]);

        $payload = $validated['card_payload'] ?? null;
        if ($validated['type'] !== 'text' && !empty($validated['entity_id']) && empty($payload)) {
            $payload = $this->buildCardPayload($validated['type'], $validated['entity_id']);
        }

        $template = ChatTemplate::create([
            'title' => trim($validated['title']),
            'category' => trim($validated['category']),
            'type' => $validated['type'],
            'message' => $validated['message'] ?? null,
            'entity_id' => $validated['entity_id'] ?? null,
            'card_payload' => $payload,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Custom Quick Action template created successfully.',
            'template' => $template,
        ]);
    }

    /**
     * Build rich card payload from target entity.
     */
    protected function buildCardPayload(string $type, int $entityId): ?array
    {
        switch ($type) {
            case 'branch':
                $b = Branch::find($entityId);
                if (!$b) return null;
                return [
                    'card_type' => 'branch',
                    'entity_id' => $b->id,
                    'title' => $b->name,
                    'subtitle' => $b->city . ' · ' . ($b->tagline ?? 'Luxury Resort & Cottage'),
                    'image_url' => $b->hero_image_url ?? $b->cover_image_url,
                    'link_url' => route('rooms.index', ['branch_id' => $b->id]),
                    'action_text' => 'Explore Branch & Stays',
                    'badge' => 'Cottage Branch',
                ];

            case 'room':
                $r = RoomType::with('branch')->find($entityId);
                if (!$r) return null;
                return [
                    'card_type' => 'room',
                    'entity_id' => $r->id,
                    'title' => $r->name,
                    'subtitle' => ($r->branch ? $r->branch->name : '') . ' · Up to ' . $r->max_guests . ' Guests',
                    'price' => '₹' . number_format($r->base_price),
                    'image_url' => $r->cover_image_url,
                    'link_url' => route('rooms.show', $r->slug),
                    'action_text' => 'View Room Details',
                    'badge' => 'Verified Cottage',
                ];

            case 'spice':
                $s = SpiceProduct::find($entityId);
                if (!$s) return null;
                return [
                    'card_type' => 'spice',
                    'entity_id' => $s->id,
                    'title' => $s->name,
                    'subtitle' => 'Pack Size: ' . ($s->package_size ?? '250g'),
                    'price' => '₹' . number_format($s->price),
                    'image_url' => $s->image_url,
                    'link_url' => route('spices.index'),
                    'action_text' => 'View in Spices Shop',
                    'badge' => 'Organic Kerala Spice',
                ];

            case 'dining':
                $d = MenuItem::find($entityId);
                if (!$d) return null;
                return [
                    'card_type' => 'dining',
                    'entity_id' => $d->id,
                    'title' => $d->name,
                    'subtitle' => $d->is_vegetarian ? 'Pure Vegetarian Special' : 'Traditional Kerala Recipe',
                    'price' => '₹' . number_format($d->price),
                    'image_url' => $d->image_url,
                    'link_url' => route('dining.index'),
                    'action_text' => 'Explore Dining Menu',
                    'badge' => $d->is_vegetarian ? 'Veg' : 'Chef Special',
                ];

            case 'nearby':
                $n = NearbyLocation::find($entityId);
                if (!$n) return null;
                return [
                    'card_type' => 'nearby',
                    'entity_id' => $n->id,
                    'title' => $n->name,
                    'subtitle' => ($n->distance_km ? $n->distance_km . ' km' : '') . ($n->travel_time ? ' · ' . $n->travel_time : ''),
                    'image_url' => $n->image_url,
                    'link_url' => route('nearby.index'),
                    'action_text' => 'Discover Location',
                    'badge' => 'Nearby Excursion',
                ];

            default:
                return null;
        }
    }

    /**
     * Moderate reviews.
     */
    public function moderateReview(Request $request, $id): JsonResponse
    {
        $review = Review::findOrFail($id);
        $status = $request->input('status'); // approved, hidden, rejected
        $reply = $request->input('staff_reply');

        $updates = ['status' => $status];
        if ($reply) {
            $updates['staff_reply'] = $reply;
            $updates['replied_by'] = Auth::id() ?? 1;
            $updates['replied_at'] = Carbon::now();
        }

        $review->update($updates);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'user_name' => Auth::user()->name ?? 'System Admin',
            'role' => Auth::user()->role ?? 'super_admin',
            'branch_id' => $review->branch_id,
            'action' => 'moderate_review',
            'entity_type' => 'Review',
            'entity_id' => $review->id,
            'new_values' => ['status' => $status],
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Review has been marked as {$status}.",
            'review' => $review,
        ]);
    }
}
