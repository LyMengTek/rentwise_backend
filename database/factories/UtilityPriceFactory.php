<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UtilityPrice;
use App\Models\UserDetail;

class UtilityPriceFactory extends Factory
{
    protected $model = UtilityPrice::class;

    public function definition(): array
    {
        return [
            'landlord_id' => UserDetail::factory(), // Ensure this field is set
            'water_price' => $this->faker->randomFloat(2, 10, 100), // Example field
            'electricity_price' => $this->faker->randomFloat(2, 20, 200), // Example field
        ];
    }
}