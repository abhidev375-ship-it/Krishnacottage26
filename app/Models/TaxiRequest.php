<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxiRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'reservation_id',
        'guest_id',
        'branch_id',
        'pickup_date',
        'pickup_time',
        'passengers_count',
        'selected_location_ids',
        'extra_locations_notes',
        'estimated_fare',
        'driver_details',
        'manager_notes',
        'status',
        'folio_charge_id',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'passengers_count' => 'integer',
            'selected_location_ids' => 'array',
            'estimated_fare' => 'decimal:2',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function folioCharge(): BelongsTo
    {
        return $this->belongsTo(FolioCharge::class, 'folio_charge_id');
    }

    /**
     * Resolve the collection of NearbyLocation models from selected_location_ids.
     */
    public function getSelectedLocationsAttribute()
    {
        if (empty($this->selected_location_ids)) {
            return collect();
        }

        return NearbyLocation::whereIn('id', $this->selected_location_ids)->get();
    }
}
