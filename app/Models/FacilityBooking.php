<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'facility_id',
        'reservation_id',
        'guest_id',
        'branch_id',
        'booking_date',
        'guests_count',
        'rate',
        'total_amount',
        'allocated_time_slot',
        'status',
        'notes',
        'folio_charge_id',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'guests_count' => 'integer',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
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
        return $this->belongsTo(FolioCharge::class);
    }
}
