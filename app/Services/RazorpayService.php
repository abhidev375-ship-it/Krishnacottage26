<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected const API_BASE = 'https://api.razorpay.com/v1';

    /**
     * Get the active Razorpay Key ID (database setting takes precedence over .env)
     */
    public function getKeyId(): string
    {
        $dbKey = Setting::get('razorpay_key_id');
        if (!empty($dbKey)) {
            return trim($dbKey);
        }
        return trim((string) config('services.razorpay.key_id', ''));
    }

    /**
     * Get the active Razorpay Key Secret
     */
    public function getKeySecret(): string
    {
        $dbSecret = Setting::get('razorpay_key_secret');
        if (!empty($dbSecret)) {
            return trim($dbSecret);
        }
        return trim((string) config('services.razorpay.key_secret', ''));
    }

    /**
     * Get the active Razorpay Webhook Secret
     */
    public function getWebhookSecret(): string
    {
        $dbSecret = Setting::get('razorpay_webhook_secret');
        if (!empty($dbSecret)) {
            return trim($dbSecret);
        }
        return trim((string) config('services.razorpay.webhook_secret', ''));
    }

    /**
     * Check if Razorpay credentials are fully configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->getKeyId()) && !empty($this->getKeySecret());
    }

    /**
     * Determine if current credentials are Test or Live mode
     */
    public function isTestMode(): bool
    {
        return str_starts_with($this->getKeyId(), 'rzp_test_');
    }

    /**
     * Create a new Order in Razorpay
     *
     * @param float $amountInRupees Exact amount in INR (e.g. 1500.50)
     * @param string $receiptId Internal identifier (e.g. KR-20260911-XXXX)
     * @param array $notes Custom key-value pairs for dashboard tracking
     * @return array
     * @throws \Exception
     */
    public function createOrder(float $amountInRupees, string $receiptId, array $notes = []): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Razorpay credentials are not configured.');
        }

        // Razorpay expects amount in smallest currency unit (paise: ₹1 = 100 paise)
        $amountInPaise = (int) round($amountInRupees * 100);

        if ($amountInPaise < 100) {
            throw new \Exception('Transaction amount must be at least ₹1.00.');
        }

        $payload = [
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => substr($receiptId, 0, 40),
            'notes' => array_merge([
                'merchant' => 'Krishna Cottage',
                'created_at' => date('Y-m-d H:i:s'),
            ], $notes),
        ];

        try {
            $response = Http::withBasicAuth($this->getKeyId(), $this->getKeySecret())
                ->timeout(12)
                ->post(self::API_BASE . '/orders', $payload);

            if ($response->successful()) {
                $orderData = $response->json();
                Log::info("Razorpay Order Created: {$orderData['id']} for Receipt: {$receiptId}");
                return $orderData;
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['description'] ?? 'Failed to communicate with Razorpay API';
            Log::error('Razorpay Order Creation Failed: ' . $response->body());
            throw new \Exception($errorMessage);
        } catch (\Throwable $e) {
            Log::error('Razorpay Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cryptographically verify the payment signature returned by Razorpay Checkout modal
     *
     * Signature Formula:
     * HMAC-SHA256(order_id + "|" + razorpay_payment_id, secret) == razorpay_signature
     *
     * @param string $orderId
     * @param string $paymentId
     * @param string $signature
     * @return bool
     */
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        if (empty($orderId) || empty($paymentId) || empty($signature)) {
            return false;
        }

        $secret = $this->getKeySecret();
        if (empty($secret)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify incoming webhook signature
     *
     * @param string $payload Raw request body string
     * @param string $signature Content of 'X-Razorpay-Signature' header
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = $this->getWebhookSecret();
        if (empty($secret)) {
            // If dedicated webhook secret not configured, fallback to Key Secret
            $secret = $this->getKeySecret();
        }

        if (empty($secret) || empty($signature)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Fetch full telemetry and payment details directly from Razorpay
     *
     * @param string $paymentId
     * @return array|null
     */
    public function fetchPayment(string $paymentId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->getKeyId(), $this->getKeySecret())
                ->timeout(10)
                ->get(self::API_BASE . "/payments/{$paymentId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("Failed to fetch Razorpay payment {$paymentId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Issue automated full or partial refund
     *
     * @param string $paymentId
     * @param float|null $amountInRupees Optional partial amount; null for 100% full refund
     * @param string $reason
     * @return array|null
     */
    public function refund(string $paymentId, ?float $amountInRupees = null, string $reason = ''): ?array
    {
        $payload = [];
        if ($amountInRupees !== null && $amountInRupees > 0) {
            $payload['amount'] = (int) round($amountInRupees * 100);
        }
        if (!empty($reason)) {
            $payload['notes'] = ['reason' => $reason];
        }

        try {
            $response = Http::withBasicAuth($this->getKeyId(), $this->getKeySecret())
                ->timeout(12)
                ->post(self::API_BASE . "/payments/{$paymentId}/refund", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Razorpay refund failed for {$paymentId}: " . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error("Razorpay refund exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Test credentials against live API by creating a 1-rupee test order
     */
    public function testConnection(?string $keyId = null, ?string $keySecret = null): array
    {
        $keyId = $keyId ? trim($keyId) : $this->getKeyId();
        $keySecret = $keySecret ? trim($keySecret) : $this->getKeySecret();

        if (empty($keyId) || empty($keySecret)) {
            return [
                'success' => false,
                'message' => 'Key ID or Key Secret is empty.',
            ];
        }

        $startTime = microtime(true);
        try {
            $response = Http::withBasicAuth($keyId, $keySecret)
                ->timeout(8)
                ->post(self::API_BASE . '/orders', [
                    'amount' => 100, // ₹1.00
                    'currency' => 'INR',
                    'receipt' => 'TEST-' . time(),
                    'notes' => ['purpose' => 'API Connectivity Verification'],
                ]);

            $latencyMs = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $isTest = str_starts_with($keyId, 'rzp_test_');
                return [
                    'success' => true,
                    'connected' => true,
                    'is_test' => $isTest,
                    'mode' => $isTest ? 'test' : 'live',
                    'key_id' => $keyId,
                    'test_order_id' => $data['id'],
                    'latency_ms' => $latencyMs,
                    'message' => 'Connected successfully! Verified with Razorpay API (' . ($isTest ? 'Test Mode' : 'Live Production') . ").",
                ];
            }

            $body = $response->json();
            return [
                'success' => false,
                'connected' => false,
                'latency_ms' => $latencyMs,
                'message' => $body['error']['description'] ?? 'Authentication failed. Please verify your keys.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'connected' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }
}
