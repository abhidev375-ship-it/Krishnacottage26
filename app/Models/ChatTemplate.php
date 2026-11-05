<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'type',
        'message',
        'entity_id',
        'card_payload',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'card_payload' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
