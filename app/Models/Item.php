<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['code', 'name', 'category_id', 'location_id', 'description', 'image_path', 'total_stock', 'available_stock', 'condition'])]
class Item extends Model
{
    use HasFactory;

    /**
     * Helper method to get image URL
     */
    public function getImageUrl(): string
    {
        if ($this->image_path && \Storage::disk('public')->exists($this->image_path)) {
            return \Storage::disk('public')->url($this->image_path);
        }
        return 'https://via.placeholder.com/400x300?text=' . urlencode($this->name);
    }

    /**
     * Helper method to check if image exists
     */
    public function hasImage(): bool
    {
        return $this->image_path && \Storage::disk('public')->exists($this->image_path);
    }

    /**
     * Helper method to delete image
     */
    public function deleteImage(): bool
    {
        if ($this->image_path && \Storage::disk('public')->exists($this->image_path)) {
            \Storage::disk('public')->delete($this->image_path);
            $this->update(['image_path' => null]);
            return true;
        }
        return false;
    }

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function conditionLogs()
    {
        return $this->hasMany(ItemConditionLog::class)->orderByDesc('created_at');
    }

    /**
     * Helper methods
     */
    public function isAvailable(): bool
    {
        return $this->available_stock > 0 && in_array($this->condition, ['Baik', 'Rusak Ringan']);
    }

    public function canBorrow(int $quantity): bool
    {
        return $this->isAvailable() && $this->available_stock >= $quantity;
    }

    public function getConditionLabel(): string
    {
        return match ($this->condition) {
            'Baik' => 'Good',
            'Rusak Ringan' => 'Slightly Damaged',
            'Rusak Berat' => 'Severely Damaged',
            'Hilang' => 'Lost',
            default => 'Unknown',
        };
    }

    public function getConditionBadgeColor(): string
    {
        return match ($this->condition) {
            'Baik' => 'success',
            'Rusak Ringan' => 'warning',
            'Rusak Berat' => 'danger',
            'Hilang' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Stock management methods
     */
    public function updateStockIn(int $quantity, ?string $notes = null, ?User $user = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $user = $user ?? auth()->user();
        
        $this->increment('total_stock', $quantity);
        $this->increment('available_stock', $quantity);
        
        StockMovement::create([
            'item_id' => $this->id,
            'type' => 'In',
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by_id' => $user->id,
        ]);
    }

    public function updateStockOut(int $quantity, ?string $notes = null, ?User $user = null, ?string $referenceType = null, ?int $referenceId = null): void
    {
        $user = $user ?? auth()->user();
        
        if ($this->available_stock >= $quantity) {
            $this->decrement('available_stock', $quantity);
        }
        
        StockMovement::create([
            'item_id' => $this->id,
            'type' => 'Out',
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by_id' => $user->id,
        ]);
    }

    public function updateCondition(string $newCondition, ?string $reason = null, ?User $user = null): void
    {
        $user = $user ?? auth()->user();
        $oldCondition = $this->condition;

        ItemConditionLog::create([
            'item_id' => $this->id,
            'old_condition' => $oldCondition,
            'new_condition' => $newCondition,
            'reason' => $reason,
            'changed_by_id' => $user->id,
        ]);

        // Update stock availability based on condition changes
        if (in_array($newCondition, ['Rusak Berat', 'Hilang']) && !in_array($oldCondition, ['Rusak Berat', 'Hilang'])) {
            // Item became damaged/lost, remove from available stock
            $this->update(['available_stock' => 0]);
        } elseif (in_array($oldCondition, ['Rusak Berat', 'Hilang']) && $newCondition === 'Baik') {
            // Item was repaired, restore available stock
            $this->update(['available_stock' => $this->total_stock]);
        }

        $this->update(['condition' => $newCondition]);
    }
}
