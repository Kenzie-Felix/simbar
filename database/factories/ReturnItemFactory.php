<?php

namespace Database\Factories;

use App\Models\ReturnItem;
use App\Models\BorrowReturn;
use App\Models\LoanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnItemFactory extends Factory
{
    protected $model = ReturnItem::class;

    public function definition(): array
    {
        return [
            'return_id' => BorrowReturn::factory(),
            'loan_item_id' => LoanItem::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'condition' => 'Baik',
            'notes' => null,
        ];
    }
}
