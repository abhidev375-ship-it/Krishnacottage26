<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpiceOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'spice_order_id',
        'spice_product_id',
        'product_name',
        'product_sku',
        'pricing_type',
        'weight_kg',
        'weight_display',
        'unit_price',
        'quantity',
        'total_price',
        'tax_rate',
        'tax_amount',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'quantity' => 'integer',
            'weight_kg' => 'decimal:3',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SpiceOrder::class, 'spice_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SpiceProduct::class, 'spice_product_id');
    }
}
