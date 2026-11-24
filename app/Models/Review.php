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
        'guest_name',
        'guest_email',
        'guest_phone',
        'room_type_id',
        'rating',
        'title',
        'comment',
        'photos',
        'videos',
        'stay_summary',
        'status',
        'staff_reply',
        'replied_by',
        'replied_at',
        'is_featured',
        'is_pinned',
        'verified_stay',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'photos' => 'array',
            'videos' => 'array',
            'is_featured' => 'boolean',
            'is_pinned' => 'boolean',
            'verified_stay' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function getEffectiveGuestNameAttribute(): string
    {
        if (!empty($this->guest_name)) {
            return $this->guest_name;
        }
        if ($this->guest) {
            return $this->guest->full_name ?: ($this->guest->first_name ?: 'Verified Guest');
        }
        return 'Verified Guest';
    }

    public function getPhotosListAttribute(): array
    {
        if (is_array($this->photos)) {
            return array_values(array_filter($this->photos));
        }
        if (is_string($this->photos) && strlen(trim($this->photos)) > 0) {
            $decoded = json_decode($this->photos, true);
            return is_array($decoded) ? array_values(array_filter($decoded)) : [$this->photos];
        }
        return [];
    }

    public function getVideosListAttribute(): array
    {
        if (is_array($this->videos)) {
            return array_values(array_filter($this->videos));
        }
        if (is_string($this->videos) && strlen(trim($this->videos)) > 0) {
            $decoded = json_decode($this->videos, true);
            return is_array($decoded) ? array_values(array_filter($decoded)) : [$this->videos];
        }
        return [];
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
