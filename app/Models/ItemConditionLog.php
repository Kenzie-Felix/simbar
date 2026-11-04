<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['item_id', 'old_condition', 'new_condition', 'reason', 'changed_by_id'])]
class ItemConditionLog extends Model
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

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_id');
    }

    /**
     * Helper methods
     */
    public function getConditionChangeLabel(): string
    {
        $oldLabel = $this->getConditionLabel($this->old_condition);
        $newLabel = $this->getConditionLabel($this->new_condition);
        return "{$oldLabel} → {$newLabel}";
    }

    private function getConditionLabel(?string $condition): string
    {
        return match ($condition) {
            'Baik' => 'Good',
            'Rusak Ringan' => 'Slightly Damaged',
            'Rusak Berat' => 'Severely Damaged',
            'Hilang' => 'Lost',
            null => 'Unknown',
            default => 'Unknown',
        };
    }
}
