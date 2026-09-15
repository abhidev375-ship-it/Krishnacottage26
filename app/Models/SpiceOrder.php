<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpiceOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_mode',
        'room_number',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'shipping_country',
        'shipping_courier',
        'tracking_number',
        'status',
        'payment_status',
        'subtotal',
        'shipping_charge',
        'tax_amount',
        'tax_rate',
        'total_amount',
        'notes',
        'cancellation_reason',
        'spice_return_rule_id',
        'refund_amount',
        'refund_status',
        'return_notes',
        'returned_at',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SpiceOrderItem::class);
    }

    public function returnRule(): BelongsTo
    {
        return $this->belongsTo(SpiceReturnRule::class, 'spice_return_rule_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function isEligibleForReturn(): bool
    {
        if (!Setting::get('spice_returns_enabled', true)) {
            return false;
        }

        $this->loadMissing('items.product');
        if ($this->items->isNotEmpty()) {
            foreach ($this->items as $item) {
                if ($item->product && !$item->product->isReturnable()) {
                    return false;
                }
            }
        }

        return !in_array($this->status, ['cancelled']) && in_array($this->refund_status, ['none', 'rejected']);
    }

    public function isReturnRequested(): bool
    {
        return $this->refund_status === 'requested';
    }

    public function isRefunded(): bool
    {
        return in_array($this->refund_status, ['approved', 'refunded']);
    }
}
