<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'code',
        'slug',
        'tagline',
        'short_description',
        'full_description',
        'address',
        'city',
        'state',
        'pincode',
        'phone',
        'email',
        'latitude',
        'longitude',
        'hero_image_url',
        'cover_image_url',
        'status',
        'is_published',
        'sort_order',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'settings' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_user')->withTimestamps();
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function foodOrders(): HasMany
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function galleryAlbums(): HasMany
    {
        return $this->hasMany(GalleryAlbum::class);
    }

    public function nearbyLocations(): HasMany
    {
        return $this->hasMany(NearbyLocation::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function taxiRequests(): HasMany
    {
        return $this->hasMany(TaxiRequest::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state ? ($this->pincode ? "{$this->state} - {$this->pincode}" : $this->state) : $this->pincode,
        ]);

        return implode(', ', $parts) ?: ($this->city ? "{$this->city}, {$this->state}" : 'Kerala, India');
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        $query = urlencode($this->name . ' ' . ($this->address ? $this->address . ' ' : '') . $this->city);
        return "https://www.google.com/maps/search/?api=1&query={$query}";
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

