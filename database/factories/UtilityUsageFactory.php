<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UtilityUsage;
use App\Models\RentalDetail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UtilityUsage>
 */
class UtilityUsageFactory extends Factory
{
    protected $model = UtilityUsage::class;

    public function definition(): array
    {
        // Use an existing RentalDetail record or create a new one if none exist
        $rental = RentalDetail::inRandomOrder()->first();
        if (!$rental) {
            $rental = RentalDetail::factory()->create();
        }

        return [
            'rental_id' => $rental->id, // Ensure this field is set
            'room_code' => $this->faker->unique()->numberBetween(100000, 999999),
            'water_usage' => $this->faker->randomFloat(2, 0, 100),
            'electricity_usage' => $this->faker->randomFloat(2, 0, 100),
            'other' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}