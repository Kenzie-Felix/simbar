<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'department'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function loanApprovalsGiven()
    {
        return $this->hasMany(Loan::class, 'approved_by_id');
    }

    public function returnVerificationsGiven()
    {
        return $this->hasMany(BorrowReturn::class, 'verified_by_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by_id');
    }

    public function itemConditionChanges()
    {
        return $this->hasMany(ItemConditionLog::class, 'changed_by_id');
    }

    /**
     * Helper methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'Staff';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
}
