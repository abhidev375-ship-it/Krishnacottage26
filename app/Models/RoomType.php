<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'room_category_id',
        'name',
        'slug',
        'code',
        'short_description',
        'description',
        'base_price',
        'weekend_price',
        'max_guests',
        'max_adults',
        'max_children',
        'bed_type',
        'size_sqft',
        'amenities',
        'cover_image_url',
        'gallery_images',
        'is_active',
        'is_bookable',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'weekend_price' => 'decimal:2',
            'max_guests' => 'integer',
            'max_adults' => 'integer',
            'max_children' => 'integer',
            'size_sqft' => 'integer',
            'amenities' => 'array',
            'gallery_images' => 'array',
            'is_active' => 'boolean',
            'is_bookable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'room_category_id');
    }

    public function amenitiesList(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_room_type')->withTimestamps();
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Normalize cover image URL so relative uploads paths never break on nested routes.
     * Treats legacy seeder Unsplash mock URLs as null.
     */
    public function getCoverImageUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $val = trim($value);
        if (str_contains($val, 'images.unsplash.com')) {
            return null;
        }
        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://') || str_starts_with($val, '/')) {
            return $val;
        }
        return '/' . $val;
    }

    /**
     * Always return gallery images as a clean flat array of string URLs.
     * Excludes legacy seeder Unsplash mock URLs.
     */
    public function getGalleryImagesAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            $cleaned = [];
            foreach ($value as $item) {
                if (is_string($item) && strlen(trim($item)) > 0) {
                    $item = trim($item);
                    if (str_contains($item, 'images.unsplash.com')) continue;
                    $cleaned[] = (str_starts_with($item, 'http://') || str_starts_with($item, 'https://') || str_starts_with($item, '/')) ? $item : '/' . $item;
                }
            }
            return array_values(array_unique($cleaned));
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $cleaned = [];
                foreach ($decoded as $item) {
                    if (is_string($item) && strlen(trim($item)) > 0) {
                        $item = trim($item);
                        if (str_contains($item, 'images.unsplash.com')) continue;
                        $cleaned[] = (str_starts_with($item, 'http://') || str_starts_with($item, 'https://') || str_starts_with($item, '/')) ? $item : '/' . $item;
                    }
                }
                return array_values(array_unique($cleaned));
            }
            $parts = array_filter(array_map('trim', explode(',', $value)));
            $cleaned = [];
            foreach ($parts as $item) {
                if (strlen($item) > 0) {
                    if (str_contains($item, 'images.unsplash.com')) continue;
                    $cleaned[] = (str_starts_with($item, 'http://') || str_starts_with($item, 'https://') || str_starts_with($item, '/')) ? $item : '/' . $item;
                }
            }
            return array_values(array_unique($cleaned));
        }
        return [];
    }

    /**
     * Booted model events for cache busting and image integrity.
     */
    protected static function booted()
    {
        static::saving(function ($roomType) {
            // If cover_image_url is empty but gallery_images has photos, auto-populate cover_image_url
            if (empty($roomType->cover_image_url) && !empty($roomType->gallery_images)) {
                $gallery = $roomType->gallery_images;
                if (is_array($gallery) && !empty($gallery[0])) {
                    $roomType->cover_image_url = $gallery[0];
                }
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('homepage_payload');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('homepage_payload');
        });
    }

    /**
     * Get all genuine photos for this suite (cover photo + gallery images).
     * Strictly only genuine room photos; never borrows branch assets or mock images.
     */
    public function getAllPhotosAttribute(): array
    {
        $photos = [];
        if (!empty($this->cover_image_url) && !str_contains($this->cover_image_url, 'images.unsplash.com')) {
            $photos[] = $this->cover_image_url;
        }
        $gallery = $this->gallery_images ?? [];
        foreach ($gallery as $img) {
            if (!empty($img) && !str_contains($img, 'images.unsplash.com') && !in_array($img, $photos)) {
                $photos[] = $img;
            }
        }
        return array_values($photos);
    }
}
