<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'purpose' => $this->faker->sentence(),
            'status' => 'Menunggu Persetujuan',
            'approved_by_id' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }
}
