<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UtilityPrice;

class UtilityPriceFactory extends Factory
{
    protected $model = UtilityPrice::class;

    public function definition(): array
    {
        return [
            'water_price' => $this->faker->randomFloat(2, 10, 100), // Example field
            'electricity_price' => $this->faker->randomFloat(2, 20, 200), // Example field
        ];
    }
}