<?php

namespace Database\Factories;

use App\Models\BorrowReturn;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowReturnFactory extends Factory
{
    protected $model = BorrowReturn::class;
    protected $table = 'returns';

    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'status' => 'Menunggu Verifikasi',
            'submitted_at' => now(),
            'verified_at' => null,
            'verified_by_id' => null,
            'rejection_reason' => null,
        ];
    }
}
