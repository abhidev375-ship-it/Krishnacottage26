<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_name',
        'stay_title',
        'avatar_url',
        'rating',
        'quote',
        'branch_id',
        'sort_order',
        'is_active',
        'is_pinned',
        'photos',
        'videos',
        'review_id',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'is_pinned' => 'boolean',
            'photos' => 'array',
            'videos' => 'array',
        ];
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
