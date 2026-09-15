<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_id',
        'sender_type',
        'user_id',
        'message',
        'target_room',
        'card_type',
        'card_payload',
        'attachments',
        'is_internal_note',
    ];

    protected function casts(): array
    {
        return [
            'card_payload' => 'array',
            'attachments' => 'array',
            'is_internal_note' => 'boolean',
        ];
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
