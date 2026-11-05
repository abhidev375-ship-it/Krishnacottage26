<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\SpiceOrder;
use App\Models\User;
use App\Services\BookingAvailabilityService;
use App\Services\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    /**
     * Handle incoming Razorpay Webhooks
     */
    public function handle(Request $request, RazorpayService $razorpayService): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        // If a webhook secret is configured, verify the signature
        if (!empty($razorpayService->getWebhookSecret())) {
            if (!$razorpayService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Razorpay Webhook Signature Verification Failed');
                return response()->json(['error' => 'Invalid webhook signature'], 400);
            }
        }

        $event = json_decode($payload, true);
        if (!$event || !isset($event['event'])) {
            return response()->json(['error' => 'Invalid event payload'], 400);
        }

        $eventType = $event['event'];
        Log::info("Razorpay Webhook Received: {$eventType}");

        switch ($eventType) {
            case 'payment.captured':
            case 'order.paid':
                $this->handlePaymentSuccess($event);
                break;

            case 'payment.failed':
                $this->handlePaymentFailed($event);
                break;
        }

        return response()->json(['status' => 'success', 'event' => $eventType]);
    }

    protected function handlePaymentSuccess(array $event): void
    {
        $paymentEntity = $event['payload']['payment']['entity'] ?? [];
        $orderEntity = $event['payload']['order']['entity'] ?? [];

        $paymentId = $paymentEntity['id'] ?? null;
        $orderId = $paymentEntity['order_id'] ?? ($orderEntity['id'] ?? null);

        if (!$paymentId) {
            return;
        }

        // Check if payment already exists in database
        $existingPayment = Payment::where('transaction_id', $paymentId)->first();
        if ($existingPayment) {
            Log::info("Razorpay Webhook: Payment {$paymentId} is already recorded.");
            return;
        }

        // Check if there is an active temporary hold matching this Razorpay order
        if ($orderId) {
            $heldReservation = app(BookingAvailabilityService::class)->findHoldByRazorpayOrderId($orderId);
            if ($heldReservation && $heldReservation->status === 'hold') {
                $notes = json_decode($heldReservation->internal_notes, true) ?: [];
                $paymentChoice = $notes['payment_choice'] ?? 'full';
                $paidAmount = ($paymentChoice === 'deposit_20') 
                    ? round($heldReservation->total_amount * 0.20, 2) 
                    : (float) $heldReservation->total_amount;
                
                $method = $paymentEntity['method'] ?? 'online';
                $creator = $heldReservation->creator ?? User::find($heldReservation->created_by) ?? User::first();

                app(BookingAvailabilityService::class)->confirmHeldReservation(
                    $heldReservation,
                    $paymentId,
                    $orderId,
                    null,
                    $method,
                    $paymentChoice,
                    $paidAmount,
                    $creator
                );

                Log::info("Razorpay Webhook: Successfully auto-confirmed held reservation {$heldReservation->booking_code} for payment {$paymentId}");
                return;
            }
        }

        // Check if there is an existing payment record with this order_id in gateway_response
        $existingOrderPayment = Payment::where('gateway_response->razorpay_order_id', $orderId)->first();
        if ($existingOrderPayment) {
            $existingOrderPayment->update([
                'transaction_id' => $paymentId,
                'status' => 'successful',
            ]);
            Log::info("Razorpay Webhook: Updated existing payment with transaction_id {$paymentId}");
            return;
        }

        Log::info("Razorpay Webhook: Payment captured {$paymentId} for Order {$orderId}");
    }

    protected function handlePaymentFailed(array $event): void
    {
        $paymentEntity = $event['payload']['payment']['entity'] ?? [];
        $paymentId = $paymentEntity['id'] ?? null;
        $errorCode = $paymentEntity['error_code'] ?? 'UNKNOWN';
        $errorDescription = $paymentEntity['error_description'] ?? 'Payment failed';

        Log::warning("Razorpay Payment Failed ({$paymentId}): [{$errorCode}] {$errorDescription}");
    }
}
