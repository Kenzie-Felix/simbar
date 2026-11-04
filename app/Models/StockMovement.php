<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['item_id', 'type', 'quantity', 'reference_type', 'reference_id', 'notes', 'created_by_id'])]
class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relationships
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Helper methods
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'In' => 'Stock In',
            'Out' => 'Stock Out',
            'Adjustment' => 'Adjustment',
            default => 'Unknown',
        };
    }

    public function getReference(): ?string
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return match ($this->reference_type) {
            'Loan' => "Loan #{$this->reference_id}",
            'Return' => "Return #{$this->reference_id}",
            'Purchase' => "Purchase #{$this->reference_id}",
            'Damage' => "Damage Report #{$this->reference_id}",
            default => "Reference #{$this->reference_id}",
        };
    }
}
