<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'google_id',
        'avatar_url',
        'is_active',
        'branch_access_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Branches assigned to this staff member.
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withTimestamps();
    }

    /**
     * Check if staff has access to a specific branch.
     */
    public function canAccessBranch(?int $branchId): bool
    {
        if ($this->role === 'super_admin' || $this->branch_access_type === 'all' || is_null($branchId)) {
            return true;
        }

        return $this->branches()->where('branches.id', $branchId)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['super_admin', 'central_manager', 'branch_manager']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function guest()
    {
        return $this->hasOne(Guest::class);
    }
}
