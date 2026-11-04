<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

#[Fillable(['user_id', 'status', 'requested_at', 'approved_at', 'approved_by_id', 'rejection_reason', 'purpose'])]
class Loan extends Model
{
    use HasFactory;

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Status Constants
     */
    const STATUS_PENDING = 'Menunggu Persetujuan';
    const STATUS_APPROVED = 'Disetujui';
    const STATUS_REJECTED = 'Ditolak';
    const STATUS_BORROWED = 'Dipinjam';
    const STATUS_RETURNED = 'Dikembalikan';

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }

    public function return()
    {
        return $this->hasOne(BorrowReturn::class);
    }

    /**
     * Status check methods
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isBorrowed(): bool
    {
        return $this->status === self::STATUS_BORROWED;
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function isOverdue(): bool
    {
        if (!$this->isBorrowed()) {
            return false;
        }

        $overdueItems = $this->loanItems()
            ->where('planned_return_at', '<', now())
            ->whereNull('returned_at')
            ->exists();

        return $overdueItems;
    }

    /**
     * Action methods
     */
    public function approve(User $approver): void
    {
        $this->update([
            'status' => self::STATUS_BORROWED,
            'approved_at' => now(),
            'approved_by_id' => $approver->id,
        ]);
    }

    public function reject(string $reason, User $rejector): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'approved_by_id' => $rejector->id,
        ]);
    }

    /**
     * Helper methods
     */
    public function getTotalItemsCount(): int
    {
        return $this->loanItems()->count();
    }

    public function getTotalQuantity(): int
    {
        return $this->loanItems()->sum('quantity');
    }

    public function daysUntilDue(): ?int
    {
        $earliest = $this->loanItems()
            ->orderBy('planned_return_at')
            ->first();

        if (!$earliest) {
            return null;
        }

        $daysUntil = now()->diffInDays($earliest->planned_return_at, false);
        return max($daysUntil, 0);
    }

    public function getEarliestReturnDate(): ?Carbon
    {
        return $this->loanItems()
            ->orderBy('planned_return_at')
            ->first()
            ?->planned_return_at;
    }
}
