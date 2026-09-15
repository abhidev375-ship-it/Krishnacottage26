<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'is_all_branches',
        'allocated_branch_ids',
        'menu_category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'tax_rate',
        'availability_state',
        'stock_quantity',
        'available_days',
        'is_available_today',
        'is_featured',
        'is_vegetarian',
        'dietary_tags',
        'prep_time_minutes',
        'image_url',
        'sort_order',
        'average_rating',
        'reviews_count',
    ];

    protected function casts(): array
    {
        return [
            'is_all_branches' => 'boolean',
            'allocated_branch_ids' => 'array',
            'price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'stock_quantity' => 'integer',
            'available_days' => 'array',
            'is_available_today' => 'boolean',
            'is_featured' => 'boolean',
            'is_vegetarian' => 'boolean',
            'dietary_tags' => 'array',
            'prep_time_minutes' => 'integer',
            'sort_order' => 'integer',
            'average_rating' => 'decimal:2',
            'reviews_count' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class, 'menu_item_modifier_group');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FoodReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(FoodReview::class)->where('status', 'approved');
    }

    public function isAvailableForBranch(?int $branchId): bool
    {
        if ($this->is_all_branches || is_null($this->branch_id)) {
            return true;
        }
        if (!$branchId) {
            return true;
        }
        if ($this->branch_id === $branchId) {
            return true;
        }
        if (!empty($this->allocated_branch_ids) && in_array($branchId, $this->allocated_branch_ids)) {
            return true;
        }
        return false;
    }

    public function isAvailableOnDay(?string $dayOfWeek = null): bool
    {
        if (!$this->is_available_today) {
            return false;
        }
        if (empty($this->available_days)) {
            return true;
        }
        $dayMap = [
            'mon' => 'mon', 'monday' => 'mon',
            'tue' => 'tue', 'tuesday' => 'tue',
            'wed' => 'wed', 'wednesday' => 'wed',
            'thu' => 'thu', 'thursday' => 'thu',
            'fri' => 'fri', 'friday' => 'fri',
            'sat' => 'sat', 'saturday' => 'sat',
            'sun' => 'sun', 'sunday' => 'sun',
        ];
        $day = strtolower($dayOfWeek ?: date('D')); // e.g. 'mon', 'tue', etc.
        $target = $dayMap[$day] ?? $day;
        $normalized = array_map(function ($d) use ($dayMap) {
            $val = strtolower(trim((string)$d));
            return $dayMap[$val] ?? $val;
        }, (array)$this->available_days);

        return in_array($target, $normalized);
    }

    public function recalculateRating(): void
    {
        $approved = $this->approvedReviews;
        $count = $approved->count();
        if ($count > 0) {
            $avg = round($approved->avg('rating'), 2);
            $this->update([
                'average_rating' => $avg,
                'reviews_count' => $count,
            ]);
        } else {
            $this->update([
                'average_rating' => 5.00,
                'reviews_count' => 0,
            ]);
        }
    }
}
