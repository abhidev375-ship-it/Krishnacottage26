<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code',
        'branch_id',
        'room_type_id',
        'room_id',
        'guest_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'rooms_count',
        'nightly_rate',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'status',
        'payment_status',
        'payment_method',
        'special_requests',
        'internal_notes',
        'hold_expires_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'cancellation_reason',
        'refunded_amount',
        'is_counter_booking',
        'counter_booked_by',
        'id_proof_type',
        'id_proof_number',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'nightly_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'is_counter_booking' => 'boolean',
            'hold_expires_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'adults' => 'integer',
            'children' => 'integer',
            'rooms_count' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function counterBooker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counter_booked_by');
    }

    public function extensionRequests(): HasMany
    {
        return $this->hasMany(StayExtensionRequest::class)->latest();
    }

    public function latestExtensionRequest(): HasOne
    {
        return $this->hasOne(StayExtensionRequest::class)->latestOfMany();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ReservationStatusLog::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function folioCharges(): HasMany
    {
        return $this->hasMany(FolioCharge::class)->latest();
    }

    public function facilityBookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class)->latest();
    }

    public function taxiRequests(): HasMany
    {
        return $this->hasMany(TaxiRequest::class)->latest();
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class)->latest('last_message_at');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function calculateFolioSummary(): array
    {
        $roomTotal = (float) $this->total_amount;
        $folioChargesTotal = (float) $this->folioCharges()->sum('amount');
        $paidTotal = (float) $this->paid_amount;
        $netDue = max(0, ($roomTotal + $folioChargesTotal) - $paidTotal);

        return [
            'room_total' => $roomTotal,
            'folio_charges_total' => $folioChargesTotal,
            'grand_total' => $roomTotal + $folioChargesTotal,
            'paid_total' => $paidTotal,
            'net_due' => round($netDue, 2),
            'is_settled' => $netDue <= 0.01,
        ];
    }

    public function scopeForBranch($query, ?int $branchId)
    {
        if ($branchId) {
            return $query->where('branch_id', $branchId);
        }
        return $query;
    }

    /**
     * Atomic check whether a physical room is available for an exact date range [ci, co).
     */
    public static function isRoomAvailableForRange(int $roomId, $checkInDate, $checkOutDate, ?int $excludeReservationId = null): bool
    {
        $ci = \Carbon\Carbon::parse($checkInDate)->toDateString();
        $co = \Carbon\Carbon::parse($checkOutDate)->toDateString();

        // Check room operational status
        $room = Room::find($roomId);
        if (!$room || in_array($room->operational_status, ['maintenance', 'blocked', 'out_of_order'])) {
            return false;
        }

        // Check overlapping reservations
        return self::where('room_id', $roomId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<', $co)
            ->where('check_out_date', '>', $ci)
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->doesntExist();
    }
}
