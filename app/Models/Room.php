<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'room_type_id',
        'room_number',
        'max_guests',
        'floor',
        'operational_status',
        'housekeeping_status',
        'notes',
    ];

    public function getMaxGuestsAttribute(): int
    {
        return (int) ($this->attributes['max_guests'] ?? ($this->roomType?->max_guests ?? 2));
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(RoomBlock::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('operational_status', 'available');
    }

    public function scopeClean($query)
    {
        return $query->where('housekeeping_status', 'clean');
    }
}
