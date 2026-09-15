<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancellationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'hours_before_checkin',
        'refund_percentage',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'hours_before_checkin' => 'integer',
            'refund_percentage' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate refund percentage and cashback amount based on hours remaining to check-in.
     * Standard resort check-in is 14:00 (2:00 PM) on the check_in_date.
     */
    public static function calculateRefund(float $paidAmount, ?int $branchId, $checkInDate): array
    {
        $checkIn = Carbon::parse($checkInDate);
        if ($checkIn->hour === 0 && $checkIn->minute === 0) {
            $checkIn->setTime(14, 0, 0);
        }

        $now = Carbon::now();
        $hoursRemaining = $now->diffInHours($checkIn, false);

        if ($hoursRemaining < 0) {
            return [
                'hours_remaining' => max(0, $hoursRemaining),
                'percentage' => 0.0,
                'cashback' => 0.0,
                'retained' => $paidAmount,
                'rule_description' => 'Check-in time has already passed. Non-refundable.',
                'matched_rule' => null,
            ];
        }

        // Fetch active rules for this branch or global fallback, sorted highest threshold first
        $rules = self::where('is_active', true)
            ->where(function ($q) use ($branchId) {
                if ($branchId) {
                    $q->where('branch_id', $branchId)->orWhereNull('branch_id');
                } else {
                    $q->whereNull('branch_id');
                }
            })
            ->orderByDesc('hours_before_checkin')
            ->get();

        $matchedRule = null;
        foreach ($rules as $rule) {
            if ($hoursRemaining >= $rule->hours_before_checkin) {
                $matchedRule = $rule;
                break;
            }
        }

        $percentage = $matchedRule ? (float) $matchedRule->refund_percentage : 0.0;
        $cashback = round(($paidAmount * $percentage) / 100, 2);
        $retained = max(0, round($paidAmount - $cashback, 2));

        return [
            'hours_remaining' => $hoursRemaining,
            'percentage' => $percentage,
            'cashback' => $cashback,
            'retained' => $retained,
            'rule_description' => $matchedRule ? $matchedRule->description : 'No matching cancellation rule. Non-refundable.',
            'matched_rule' => $matchedRule,
        ];
    }
}
