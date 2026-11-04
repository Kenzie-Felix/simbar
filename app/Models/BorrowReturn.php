<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['loan_id', 'status', 'submitted_at', 'verified_at', 'verified_by_id', 'rejection_reason'])]
class BorrowReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Status Constants
     */
    const STATUS_PENDING = 'Menunggu Verifikasi';
    const STATUS_ACCEPTED = 'Terima';
    const STATUS_REJECTED = 'Tolak';

    /**
     * Relationships
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    /**
     * Status check methods
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Action methods
     */
    public function verify(User $verifier): void
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'verified_at' => now(),
            'verified_by_id' => $verifier->id,
        ]);
    }

    public function reject(string $reason, User $rejector): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verified_at' => now(),
            'verified_by_id' => $rejector->id,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Helper methods
     */
    public function getTotalReturnedQuantity(): int
    {
        return $this->returnItems()->sum('quantity');
    }

    public function getReturnedItemsCount(): int
    {
        return $this->returnItems()->count();
    }
}
