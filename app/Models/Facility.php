<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'category',
        'is_bookable',
        'rate',
        'has_scheduling',
        'short_description',
        'description',
        'operating_hours',
        'image_url',
        'is_featured',
        'is_published',
        'show_in_navigation',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_bookable' => 'boolean',
            'has_scheduling' => 'boolean',
            'rate' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'show_in_navigation' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }

    public function getBranchLabelAttribute(): string
    {
        return $this->branch ? $this->branch->name : 'All Branches';
    }
}
