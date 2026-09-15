<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpiceProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'spice_category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'weight_grams',
        'package_size',
        'selling_mode',
        'price',
        'price_per_kg',
        'min_loose_weight_kg',
        'tax_rate',
        'compare_at_price',
        'stock_quantity',
        'reserved_quantity',
        'low_stock_threshold',
        'image_url',
        'gallery',
        'is_featured',
        'is_published',
        'is_available',
        'is_active',
        'is_returnable',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'min_loose_weight_kg' => 'decimal:3',
            'tax_rate' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'weight_grams' => 'integer',
            'stock_quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'is_returnable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function isReturnable(): bool
    {
        if (!Setting::get('spice_returns_enabled', true)) {
            return false;
        }

        return (bool) ($this->is_returnable ?? true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('is_available', true)
            ->where('is_published', true)
            ->where('status', '!=', 'inactive');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SpiceCategory::class, 'spice_category_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(SpiceInventoryLog::class)->latest('created_at');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(SpiceOrderItem::class);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    public function isPacketAvailable(): bool
    {
        return in_array($this->selling_mode, ['packet', 'both']);
    }

    public function isLooseAvailable(): bool
    {
        return in_array($this->selling_mode, ['loose', 'both']);
    }

    /**
     * Calculate line price and tax for packet or loose kg quantity.
     */
    public function calculatePricing(string $mode, float $amount): array
    {
        $taxRate = (float)($this->tax_rate ?: 5.00);
        if ($mode === 'loose') {
            $unitPrice = (float)($this->price_per_kg ?: round($this->price * (1000 / ($this->weight_grams ?: 100)), 2));
            $subtotal = round($unitPrice * $amount, 2);
            $tax = round($subtotal * ($taxRate / 100), 2);
            return [
                'pricing_type' => 'loose',
                'weight_kg' => $amount,
                'weight_display' => number_format($amount, 2) . ' kg loose',
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $tax,
                'total' => $subtotal + $tax,
            ];
        }

        // Packet
        $qty = max(1, (int)$amount);
        $unitPrice = (float)$this->price;
        $subtotal = round($unitPrice * $qty, 2);
        $tax = round($subtotal * ($taxRate / 100), 2);
        $label = ($this->package_size ?: ($this->weight_grams . 'g Pack')) . ($qty > 1 ? " × {$qty}" : '');
        return [
            'pricing_type' => 'packet',
            'quantity' => $qty,
            'weight_display' => $label,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $tax,
            'total' => $subtotal + $tax,
        ];
    }
}
