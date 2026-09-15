<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NearbyLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'category',
        'description',
        'distance_km',
        'travel_time',
        'address',
        'image_url',
        'is_available',
        'is_taxi_available',
        'latitude',
        'longitude',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_available' => 'boolean',
            'is_taxi_available' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope only locations that are open/available for guest visits.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope only locations with taxi/cab service available.
     */
    public function scopeTaxiAvailable($query)
    {
        return $query->where('is_taxi_available', true);
    }
}
