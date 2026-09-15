<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StayExtensionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'guest_id',
        'user_id',
        'current_checkout_date',
        'requested_checkout_date',
        'extra_nights',
        'allocation_type',
        'allocated_room_ids',
        'standard_amount',
        'offered_amount',
        'manager_discount_percentage',
        'status',
        'payment_status',
        'payment_method',
        'paid_amount',
        'guest_notes',
        'manager_notes',
        'reviewed_by',
        'reviewed_at',
        'approved_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'current_checkout_date' => 'date',
            'requested_checkout_date' => 'date',
            'extra_nights' => 'integer',
            'allocated_room_ids' => 'array',
            'standard_amount' => 'decimal:2',
            'offered_amount' => 'decimal:2',
            'manager_discount_percentage' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getAllocatedRoomsAttribute()
    {
        $ids = $this->allocated_room_ids;
        if (empty($ids) || !is_array($ids)) {
            return collect();
        }
        return Room::with('roomType')->whereIn('id', $ids)->get();
    }
}
