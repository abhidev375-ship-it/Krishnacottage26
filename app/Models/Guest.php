<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'secondary_phone',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'id_proof_type',
        'id_proof_number',
        'vip_level',
        'preferences',
        'internal_notes',
        'total_stays',
        'total_spent',
    ];

    protected function casts(): array
    {
        return [
            'total_spent' => 'decimal:2',
            'total_stays' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function foodOrders(): HasMany
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function spiceOrders(): HasMany
    {
        return $this->hasMany(SpiceOrder::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
