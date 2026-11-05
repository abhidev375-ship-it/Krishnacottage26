<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'client_session_id',
        'branch_id',
        'guest_id',
        'user_id',
        'reservation_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'topic',
        'subject',
        'status',
        'priority',
        'assigned_to',
        'locked_by',
        'locked_at',
        'has_unread_messages',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'has_unread_messages' => 'boolean',
            'last_message_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(EnquiryMessage::class);
    }

    public function latestMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EnquiryMessage::class)->latestOfMany();
    }

    /**
     * Check if conversation is currently locked
     */
    public function isLocked(): bool
    {
        return !is_null($this->locked_by);
    }

    /**
     * Check if conversation is locked by a specific user
     */
    public function isLockedBy(?int $userId): bool
    {
        return $this->locked_by !== null && (int)$this->locked_by === (int)$userId;
    }

    /**
     * Check if a staff member can access/interact with this chat
     */
    public function canStaffAccess(?User $staff): bool
    {
        if (!$staff) return false;
        if ($staff->isSuperAdmin()) return true; // Super admin can oversee/override
        if (!$this->isLocked()) return true; // Unlocked chat can be viewed/claimed
        return (int)$this->locked_by === (int)$staff->id;
    }
}
