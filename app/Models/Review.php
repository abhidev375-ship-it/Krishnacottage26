<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'branch_id',
        'guest_id',
        'room_type_id',
        'rating',
        'title',
        'comment',
        'stay_summary',
        'status',
        'staff_reply',
        'replied_by',
        'replied_at',
        'is_featured',
        'verified_stay',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_featured' => 'boolean',
            'verified_stay' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
