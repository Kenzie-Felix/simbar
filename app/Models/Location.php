<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description'])]
class Location extends Model
{
    use HasFactory;

    /**
     * Relationships
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Helper methods
     */
    public function itemCount(): int
    {
        return $this->items()->count();
    }
}
