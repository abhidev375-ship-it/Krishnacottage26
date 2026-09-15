<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpiceReturnRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'applies_to',
        'time_limit_hours',
        'refund_percentage',
        'handling_fee',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'time_limit_hours' => 'integer',
            'refund_percentage' => 'decimal:2',
            'handling_fee' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SpiceOrder::class, 'spice_return_rule_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate refund eligibility, cashback amount, and matched rule for a given spice order.
     *
     * @param SpiceOrder $order
     * @param string|null $requestType 'cancellation' (pre-dispatch) or 'return' (post-delivery)
     * @return array
     */
    public static function calculateRefund(SpiceOrder $order, ?string $requestType = null): array
    {
        $now = Carbon::now();
        $isDispatchedOrDelivered = in_array($order->status, ['shipped', 'delivered']);
        
        // Determine whether this is evaluated as pre-dispatch cancellation or post-delivery return
        if (!$requestType) {
            $requestType = $isDispatchedOrDelivered ? 'return' : 'cancellation';
        }

        $totalPaid = (float) $order->total_amount;

        // 1. Global Returns Disabled Check
        if (!Setting::get('spice_returns_enabled', true)) {
            return [
                'eligible' => false,
                'request_type' => $requestType,
                'hours_elapsed' => 0,
                'percentage' => 0.0,
                'refund_percentage' => 0.0,
                'cashback' => 0.0,
                'handling_fee' => 0.0,
                'retained' => $totalPaid,
                'rule_name' => 'Returns Disabled',
                'rule_description' => 'This product does not have return policies.',
                'matched_rule' => null,
            ];
        }

        // 2. Per-Product Returnability Check
        $order->loadMissing('items.product');
        $hasNonReturnable = $order->items->contains(function ($item) {
            return $item->product && !$item->product->isReturnable();
        });
        if ($hasNonReturnable) {
            return [
                'eligible' => false,
                'request_type' => $requestType,
                'hours_elapsed' => 0,
                'percentage' => 0.0,
                'refund_percentage' => 0.0,
                'cashback' => 0.0,
                'handling_fee' => 0.0,
                'retained' => $totalPaid,
                'rule_name' => 'No Return Policy',
                'rule_description' => 'This product does not have return policies.',
                'matched_rule' => null,
            ];
        }

        $appliesFilter = ($requestType === 'cancellation') ? 'before_dispatch' : 'after_delivery';

        // Reference timestamp for time elapsed calculation
        $referenceTime = $order->created_at ?: $now;
        if ($requestType === 'return' && $order->delivered_at) {
            $referenceTime = $order->delivered_at;
        }

        $hoursElapsed = max(0, (int) $referenceTime->diffInHours($now));

        // Query active rules matching the application stage or 'general' fallback
        $rules = self::active()
            ->where(function ($q) use ($appliesFilter) {
                $q->where('applies_to', $appliesFilter)->orWhere('applies_to', 'general');
            })
            ->orderBy('sort_order')
            ->get();

        $matchedRule = null;
        foreach ($rules as $rule) {
            // Rule matches if elapsed hours is within rule's time limit
            if ($hoursElapsed <= $rule->time_limit_hours) {
                $matchedRule = $rule;
                break;
            }
        }

        $totalPaid = (float) $order->total_amount;

        if (!$matchedRule) {
            return [
                'eligible' => false,
                'request_type' => $requestType,
                'hours_elapsed' => $hoursElapsed,
                'percentage' => 0.0,
                'refund_percentage' => 0.0,
                'cashback' => 0.0,
                'handling_fee' => 0.0,
                'retained' => $totalPaid,
                'rule_name' => 'Window Expired',
                'rule_description' => "Return/cancellation window ({$hoursElapsed}h elapsed) has expired for this order under Krishna Estate policy.",
                'matched_rule' => null,
            ];
        }

        $percentage = (float) $matchedRule->refund_percentage;
        $handlingFee = (float) $matchedRule->handling_fee;

        $grossRefund = round(($totalPaid * $percentage) / 100, 2);
        $netCashback = max(0.0, round($grossRefund - $handlingFee, 2));
        $retained = max(0.0, round($totalPaid - $netCashback, 2));

        return [
            'eligible' => true,
            'request_type' => $requestType,
            'hours_elapsed' => $hoursElapsed,
            'time_limit_hours' => $matchedRule->time_limit_hours,
            'percentage' => $percentage,
            'refund_percentage' => $percentage,
            'cashback' => $netCashback,
            'handling_fee' => $handlingFee,
            'retained' => $retained,
            'rule_name' => $matchedRule->name,
            'rule_description' => $matchedRule->description,
            'matched_rule' => $matchedRule,
        ];
    }
}
