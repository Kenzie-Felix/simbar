<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['return_id', 'loan_item_id', 'quantity', 'condition', 'notes'])]
class ReturnItem extends Model
{
    use HasFactory;

    /**
     * Relationships
     */
    public function return()
    {
        return $this->belongsTo(BorrowReturn::class, 'return_id');
    }

    public function loanItem()
    {
        return $this->belongsTo(LoanItem::class);
    }
}
