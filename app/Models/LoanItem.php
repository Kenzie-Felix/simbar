<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['loan_id', 'item_id', 'quantity', 'planned_return_at', 'returned_at', 'return_quantity', 'return_condition'])]
class LoanItem extends Model
{
    use HasFactory;

    protected $casts = [
        'planned_return_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function returnItem()
    {
        return $this->hasOne(ReturnItem::class);
    }

    /**
     * Status check methods
     */
    public function isOverdue(): bool
    {
        return $this->planned_return_at < now() && is_null($this->returned_at);
    }

    public function isReturned(): bool
    {
        return !is_null($this->returned_at);
    }

    public function daysUntilDue(): int
    {
        return max(0, (int) now()->diffInDays($this->planned_return_at, false));
    }

    public function getReturnedPercentage(): float
    {
        if ($this->quantity === 0) {
            return 0;
        }
        return ($this->return_quantity / $this->quantity) * 100;
    }
}
