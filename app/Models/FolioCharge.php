<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FolioCharge extends Model
{
    use HasFactory;

    protected $table = 'reservation_folio_charges';

    protected $fillable = [
        'reservation_id',
        'branch_id',
        'category',
        'title',
        'description',
        'amount',
        'is_paid',
        'added_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
