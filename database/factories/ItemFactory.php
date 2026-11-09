<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'code' => strtoupper($this->faker->unique()->lexify('???###')),
            'description' => $this->faker->sentence(),
            'category_id' => Category::factory(),
            'location_id' => Location::factory(),
            'total_stock' => $this->faker->numberBetween(10, 100),
            'available_stock' => $this->faker->numberBetween(5, 50),
            'condition' => 'Baik',
        ];
    }
}
