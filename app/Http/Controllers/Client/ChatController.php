<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\Guest;
use App\Models\Reservation;
use App\Services\SmsNotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Initialize or retrieve active conversation for client.
     */
    public function init(Request $request): JsonResponse
    {
        $sessionId = $request->cookie('krishna_chat_session') ?? $request->header('X-Chat-Session-Id') ?? Str::uuid()->toString();
        $user = Auth::user();

        // 1. Search for existing conversation
        $enquiry = null;
        if ($user) {
            $enquiry = Enquiry::with(['messages' => function ($q) {
                    $q->where('is_internal_note', false)->orderBy('created_at', 'asc');
                }, 'branch', 'reservation.room', 'reservation.roomType'])
                ->where('user_id', $user->id)
                ->whereNotIn('status', ['closed'])
                ->latest('last_message_at')
                ->first();
        }

        if (!$enquiry) {
            $enquiry = Enquiry::with(['messages' => function ($q) {
                    $q->where('is_internal_note', false)->orderBy('created_at', 'asc');
                }, 'branch', 'reservation.room', 'reservation.roomType'])
                ->where('client_session_id', $sessionId)
                ->whereNotIn('status', ['closed'])
                ->latest('last_message_at')
                ->first();
        }

        // 2. Look up active or upcoming stay
        $today = Carbon::today();
        $activeReservation = null;

        if ($user) {
            $activeReservation = Reservation::with(['branch', 'roomType', 'room'])
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('guest_id', $user->guest ? $user->guest->id : 0)
                      ->orWhereHas('guest', function ($gq) use ($user) {
                          $gq->where('email', $user->email)
                             ->when($user->phone, fn($pq) => $pq->orWhere('phone', $user->phone));
                      });
                })
                ->whereIn('status', ['checked_in', 'confirmed'])
                ->where('check_out_date', '>=', $today)
                ->orderByRaw("FIELD(status, 'checked_in', 'confirmed')")
                ->orderBy('check_in_date', 'asc')
                ->first();
        }

        if (!$activeReservation && $enquiry) {
            if ($enquiry->reservation_id) {
                $activeReservation = Reservation::with(['branch', 'roomType', 'room'])->find($enquiry->reservation_id);
            } elseif ($enquiry->customer_email || $enquiry->customer_phone) {
                $activeReservation = Reservation::with(['branch', 'roomType', 'room'])
                    ->where(function ($q) use ($enquiry) {
                        if ($enquiry->guest_id) {
                            $q->where('guest_id', $enquiry->guest_id);
                        }
                        $q->orWhereHas('guest', function ($gq) use ($enquiry) {
                            $gq->where(function ($w) use ($enquiry) {
                                if ($enquiry->customer_email) $w->orWhere('email', $enquiry->customer_email);
                                if ($enquiry->customer_phone) $w->orWhere('phone', $enquiry->customer_phone);
                            });
                        });
                    })
                    ->whereIn('status', ['checked_in', 'confirmed'])
                    ->where('check_out_date', '>=', $today)
                    ->orderByRaw("FIELD(status, 'checked_in', 'confirmed')")
                    ->orderBy('check_in_date', 'asc')
                    ->first();
            }
        }

        // 3. Create new conversation if none active
        if (!$enquiry) {
            $ticketNumber = 'ENQ-' . date('Y') . '-' . strtoupper(Str::random(5));
            $enquiry = Enquiry::create([
                'ticket_number' => $ticketNumber,
                'client_session_id' => $sessionId,
                'user_id' => $user ? $user->id : null,
                'branch_id' => $activeReservation ? $activeReservation->branch_id : null,
                'reservation_id' => $activeReservation ? $activeReservation->id : null,
                'guest_id' => $activeReservation ? $activeReservation->guest_id : ($user && $user->guest ? $user->guest->id : null),
                'customer_name' => $user ? $user->name : ($activeReservation && $activeReservation->guest ? $activeReservation->guest->full_name : 'Guest Visitor'),
                'customer_email' => $user ? $user->email : ($activeReservation && $activeReservation->guest ? $activeReservation->guest->email : 'guest_' . substr($sessionId, 0, 8) . '@krishnaresorts.com'),
                'customer_phone' => $user ? $user->phone : ($activeReservation && $activeReservation->guest ? $activeReservation->guest->phone : null),
                'topic' => $activeReservation ? 'booking_related' : 'general',
                'subject' => $activeReservation ? ('Concierge: ' . ($activeReservation->branch ? $activeReservation->branch->name : 'Resort')) : 'Concierge Live Chat',
                'status' => 'new',
                'priority' => $activeReservation ? 'high' : 'medium',
                'has_unread_messages' => false,
                'last_message_at' => Carbon::now(),
            ]);

            // Welcome greeting message from Concierge
            $welcomeText = "Namaste! Welcome to Krishna Resorts & Cottages. How may our concierge team assist your holiday plans, room selection, or dining experience today?";
            if ($activeReservation) {
                $bName = $activeReservation->branch ? $activeReservation->branch->name : 'our resort';
                $roomLabel = $activeReservation->room ? "Room {$activeReservation->room->room_number}" : ($activeReservation->roomType ? $activeReservation->roomType->name : 'cottage');
                $welcomeText = "Namaste! Welcome to {$bName}. Our concierge team and branch manager are at your service for {$roomLabel}. Please let us know if you need housekeeping, dining service, farm fresh spices, or local excursions!";
            }

            EnquiryMessage::create([
                'enquiry_id' => $enquiry->id,
                'sender_type' => 'staff',
                'user_id' => null,
                'message' => $welcomeText,
                'is_internal_note' => false,
            ]);

            $enquiry->load(['messages' => function ($q) {
                $q->where('is_internal_note', false)->orderBy('created_at', 'asc');
            }, 'branch']);
        } else {
            // Link existing enquiry with active reservation if found
            if ($activeReservation) {
                $updates = [];
                if ($enquiry->reservation_id !== $activeReservation->id) {
                    $updates['reservation_id'] = $activeReservation->id;
                }
                if ($enquiry->branch_id !== $activeReservation->branch_id) {
                    $updates['branch_id'] = $activeReservation->branch_id;
                }
                if ($activeReservation->guest_id && $enquiry->guest_id !== $activeReservation->guest_id) {
                    $updates['guest_id'] = $activeReservation->guest_id;
                }
                if ($user && !$enquiry->user_id) {
                    $updates['user_id'] = $user->id;
                }
                if (!empty($updates)) {
                    $enquiry->update($updates);
                    $enquiry->load('branch');
                }
            }
        }

        // 4. Compute active stay details and assigned rooms
        $activeStay = null;
        if ($activeReservation) {
            $guestReservations = Reservation::with(['branch', 'room', 'roomType'])
                ->where(function ($q) use ($activeReservation) {
                    if ($activeReservation->guest_id) {
                        $q->where('guest_id', $activeReservation->guest_id);
                    } else {
                        $q->where('id', $activeReservation->id);
                    }
                })
                ->whereIn('status', ['checked_in', 'confirmed'])
                ->where('check_out_date', '>=', $today)
                ->get();

            $uniqueBranchCount = $guestReservations->pluck('branch_id')->unique()->count();
            $isMultiBranch = $uniqueBranchCount > 1;

            $roomNumbers = [];
            if ($activeReservation->room) {
                $roomNumbers[] = (string) $activeReservation->room->room_number;
            }
            foreach ($guestReservations->where('branch_id', $activeReservation->branch_id) as $otherRes) {
                if ($otherRes->room && !in_array((string)$otherRes->room->room_number, $roomNumbers)) {
                    $roomNumbers[] = (string) $otherRes->room->room_number;
                }
            }

            $totalRoomsExpected = max(count($roomNumbers), (int) ($activeReservation->rooms_count ?? 1));
            if (count($roomNumbers) < $totalRoomsExpected) {
                $startNum = !empty($roomNumbers) && is_numeric($roomNumbers[0]) ? (int)$roomNumbers[0] : 101;
                while (count($roomNumbers) < $totalRoomsExpected) {
                    $candidate = (string)($startNum + count($roomNumbers));
                    if (!in_array($candidate, $roomNumbers)) {
                        $roomNumbers[] = $candidate;
                    } else {
                        $roomNumbers[] = "Unit " . (count($roomNumbers) + 1);
                    }
                }
            }

            $isInHouse = ($activeReservation->status === 'checked_in') || 
                         ($activeReservation->status === 'confirmed' && Carbon::parse($activeReservation->check_in_date)->isToday());

            $activeStay = [
                'is_in_house' => $isInHouse,
                'booking_code' => $activeReservation->booking_code,
                'reservation_id' => $activeReservation->id,
                'branch_id' => $activeReservation->branch_id,
                'branch_name' => $activeReservation->branch ? $activeReservation->branch->name : 'Resort Central',
                'branch_phone' => $activeReservation->branch ? $activeReservation->branch->phone : null,
                'room_type' => $activeReservation->roomType ? $activeReservation->roomType->name : 'Resort Cottage',
                'status' => $activeReservation->status,
                'check_in' => Carbon::parse($activeReservation->check_in_date)->format('d M Y'),
                'check_out' => Carbon::parse($activeReservation->check_out_date)->format('d M Y'),
                'rooms' => $roomNumbers,
                'is_multi_room' => count($roomNumbers) > 1 || ((int)($activeReservation->rooms_count ?? 1) > 1),
                'is_multi_branch' => $isMultiBranch,
                'rooms_count' => $totalRoomsExpected,
            ];

            if ($isInHouse) {
                $branchDiningItems = \App\Models\MenuItem::where(function($q) use ($activeReservation) {
                        $q->where('branch_id', $activeReservation->branch_id)
                          ->orWhere('is_all_branches', true)
                          ->orWhereNull('branch_id')
                          ->orWhereJsonContains('allocated_branch_ids', (int)$activeReservation->branch_id);
                    })
                    ->where('availability_state', 'in_stock')
                    ->where('is_available_today', true)
                    ->take(8)
                    ->get(['id', 'name', 'price', 'is_vegetarian', 'image_url', 'prep_time_minutes']);
                $activeStay['dining_items'] = $branchDiningItems;
            }
        }

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'enquiry' => [
                'id' => $enquiry->id,
                'ticket_number' => $enquiry->ticket_number,
                'customer_name' => $enquiry->customer_name,
                'customer_email' => $enquiry->customer_email,
                'customer_phone' => $enquiry->customer_phone,
                'status' => $enquiry->status,
                'is_locked' => $enquiry->isLocked(),
                'branch_id' => $enquiry->branch_id,
                'branch_name' => $enquiry->branch ? $enquiry->branch->name : 'Resort Central',
            ],
            'active_stay' => $activeStay,
            'dining_items' => $activeStay['dining_items'] ?? [],
            'messages' => $enquiry->messages,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ] : null,
        ])->cookie('krishna_chat_session', $sessionId, 60 * 24 * 30); // 30 days cookie
    }

    /**
     * Poll recent messages for real-time chat updates.
     */
    public function messages(Request $request, int $enquiryId): JsonResponse
    {
        $enquiry = Enquiry::findOrFail($enquiryId);
        $afterId = (int) $request->query('after_id', 0);

        $messages = $enquiry->messages()
            ->where('is_internal_note', false)
            ->when($afterId > 0, function ($q) use ($afterId) {
                $q->where('id', '>', $afterId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'enquiry_id' => $enquiry->id,
            'status' => $enquiry->status,
            'is_locked' => $enquiry->isLocked(),
            'locked_by' => $enquiry->lockedBy ? $enquiry->lockedBy->name : null,
            'messages' => $messages,
        ]);
    }

    /**
     * Send customer message & dispatch multi-recipient SMS alerts.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'message' => 'required|string|max:2000',
            'customer_name' => 'nullable|string|max:120',
            'customer_email' => 'nullable|email|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'target_room' => 'nullable|string|max:60',
            'card_type' => 'nullable|string|max:50',
            'card_payload' => 'nullable|array',
        ]);

        $enquiry = Enquiry::findOrFail($validated['enquiry_id']);
        $user = Auth::user();

        // 1. Update customer profile details if provided
        $updates = [
            'last_message_at' => Carbon::now(),
            'has_unread_messages' => true,
        ];
        if ($user && !$enquiry->user_id) {
            $updates['user_id'] = $user->id;
            $updates['customer_name'] = $user->name;
            $updates['customer_email'] = $user->email;
            if ($user->phone) $updates['customer_phone'] = $user->phone;
        } elseif (!empty($validated['customer_name'])) {
            $updates['customer_name'] = trim($validated['customer_name']);
            if (!empty($validated['customer_email'])) $updates['customer_email'] = trim($validated['customer_email']);
            if (!empty($validated['customer_phone'])) $updates['customer_phone'] = trim($validated['customer_phone']);
        }

        if (in_array($enquiry->status, ['resolved', 'closed'])) {
            $updates['status'] = 'in_progress';
        }
        $enquiry->update($updates);

        // 2. Save Message with target_room if provided
        $targetRoom = !empty($validated['target_room']) && $validated['target_room'] !== 'all' ? trim($validated['target_room']) : null;

        $msg = EnquiryMessage::create([
            'enquiry_id' => $enquiry->id,
            'sender_type' => 'customer',
            'user_id' => $user ? $user->id : null,
            'message' => trim($validated['message']),
            'target_room' => $targetRoom,
            'card_type' => $validated['card_type'] ?? null,
            'card_payload' => $validated['card_payload'] ?? null,
            'is_internal_note' => false,
        ]);

        // 3. Dispatch Multi-Recipient SMS Alert (Branch Manager for upcoming bookings + Central Admin)
        try {
            app(SmsNotificationService::class)->sendCustomerMessageAlert($enquiry, $msg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to dispatch chat SMS: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'enquiry' => [
                'id' => $enquiry->id,
                'status' => $enquiry->status,
                'is_locked' => $enquiry->isLocked(),
            ],
        ]);
    }
}
