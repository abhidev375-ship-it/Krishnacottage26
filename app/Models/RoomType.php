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
}
