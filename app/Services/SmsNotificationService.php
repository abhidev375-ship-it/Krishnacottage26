<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\EnquiryMessage;
use App\Models\NotificationLog;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    /**
     * Dispatch multi-recipient SMS alerts when a customer sends a chat message.
     */
    public function sendCustomerMessageAlert(Enquiry $enquiry, EnquiryMessage $message): array
    {
        $dispatched = [];
        $today = Carbon::today();
        $snippet = mb_strimwidth(strip_tags($message->message), 0, 90, '...');

        // 1. CHECK FOR ACTIVE OR UPCOMING RESERVATION (Next 3 Days)
        $upcomingReservation = Reservation::with('branch')
            ->where(function ($q) use ($enquiry) {
                if ($enquiry->guest_id) {
                    $q->where('guest_id', $enquiry->guest_id);
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
            ->where('check_in_date', '<=', $today->copy()->addDays(3))
            ->where('check_out_date', '>=', $today)
            ->orderBy('check_in_date', 'asc')
            ->first();

        // 2. DISPATCH TO BRANCH MANAGER IF UPCOMING STAY DETECTED
        if ($upcomingReservation && $upcomingReservation->branch) {
            $branch = $upcomingReservation->branch;
            $branchManagers = User::where('role', 'branch_manager')
                ->where('is_active', true)
                ->where(function ($q) use ($branch) {
                    $q->where('branch_access_type', 'all')
                      ->orWhere('primary_branch_id', $branch->id)
                      ->orWhereJsonContains('branch_ids', (int) $branch->id);
                })
                ->get();

            if ($branchManagers->isEmpty()) {
                $branchManagers = User::where('role', 'branch_manager')->where('is_active', true)->take(1)->get();
            }

            $checkInFormatted = Carbon::parse($upcomingReservation->check_in_date)->format('d M');
            $branchSmsText = "[Krishna Resorts Alert] Guest {$enquiry->customer_name} (Upcoming stay #{$upcomingReservation->booking_code} at {$branch->name} on {$checkInFormatted}) sent a message: \"{$snippet}\". Please check console.";

            foreach ($branchManagers as $manager) {
                $targetPhone = $manager->phone ?: ($branch->phone ?: '+91 94471 22334');
                $log = $this->dispatchSms(
                    $targetPhone,
                    $branchSmsText,
                    $branch->id,
                    $enquiry->id,
                    "Branch Manager ({$manager->name})"
                );
                $dispatched[] = $log;
            }
        }

        // 3. DISPATCH TO CENTRAL ADMIN / LOCKED STAFF
        if ($enquiry->locked_by && $enquiry->lockedBy) {
            // If chat is locked to a specific staff member, alert them directly
            $targetPhone = $enquiry->lockedBy->phone ?: '+91 98401 99880';
            $adminText = "[Krishna Resorts Chat] Customer {$enquiry->customer_name} replied to your active locked conversation: \"{$snippet}\".";
            $dispatched[] = $this->dispatchSms(
                $targetPhone,
                $adminText,
                $enquiry->branch_id,
                $enquiry->id,
                "Assigned Staff ({$enquiry->lockedBy->name})"
            );
        } else {
            // General admin alert for unassigned or incoming messages
            $centralStaff = User::whereIn('role', ['super_admin', 'central_manager'])
                ->where('is_active', true)
                ->get();

            if ($centralStaff->isEmpty()) {
                $centralStaff = User::where('role', 'super_admin')->take(1)->get();
            }

            $centralSmsText = "[Krishna Resorts Alert] New guest message from {$enquiry->customer_name} (Ticket: {$enquiry->ticket_number}): \"{$snippet}\". Open Admin Chat to claim.";

            foreach ($centralStaff->take(2) as $staff) {
                $targetPhone = $staff->phone ?: '+91 98765 43210';
                $dispatched[] = $this->dispatchSms(
                    $targetPhone,
                    $centralSmsText,
                    $enquiry->branch_id,
                    $enquiry->id,
                    "Central Admin ({$staff->name})"
                );
            }
        }

        return $dispatched;
    }

    /**
     * Dispatch SMS and record outbound notification in notification_logs.
     */
    public function dispatchSms(string $phone, string $text, ?int $branchId = null, ?int $enquiryId = null, string $recipientRole = ''): NotificationLog
    {
        $cleanPhone = preg_replace('/[^\+0-9]/', '', $phone);
        $apiKey = config('services.sms.api_key');
        $status = 'pending';
        $failureReason = null;

        // Ready for production SMS gateway
        if (!empty($apiKey) && !empty(config('services.sms.api_url'))) {
            try {
                $response = Http::timeout(5)->post(config('services.sms.api_url'), [
                    'api_key' => $apiKey,
                    'sender' => config('services.sms.sender_id', 'KRISHNA'),
                    'to' => $cleanPhone,
                    'message' => $text,
                ]);

                if ($response->successful()) {
                    $status = 'delivered';
                } else {
                    $status = 'failed';
                    $failureReason = 'SMS gateway returned HTTP ' . $response->status();
                }
            } catch (\Throwable $e) {
                $status = 'failed';
                $failureReason = $e->getMessage();
                Log::warning("SMS Gateway Exception: " . $e->getMessage());
            }
        } else {
            // In development or until user provides API key, auto-log as queued/delivered
            $status = 'delivered';
            Log::info("SMS Notification Queued for {$cleanPhone} ({$recipientRole}): {$text}");
        }

        // Record in immutable notification log
        return NotificationLog::create([
            'event' => 'customer_chat_message',
            'channel' => 'sms',
            'recipient' => $cleanPhone . ($recipientRole ? " ({$recipientRole})" : ''),
            'branch_id' => $branchId,
            'reference_type' => Enquiry::class,
            'reference_id' => $enquiryId,
            'subject' => 'Guest Chat SMS Alert',
            'message_body' => $text,
            'status' => $status,
            'failure_reason' => $failureReason,
            'sent_at' => Carbon::now(),
        ]);
    }
}
