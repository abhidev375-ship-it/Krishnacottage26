<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'channel',
        'recipient',
        'branch_id',
        'reference_type',
        'reference_id',
        'subject',
        'message_body',
        'status',
        'failure_reason',
        'retry_count',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'retry_count' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
