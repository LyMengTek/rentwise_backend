<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PreviousUtilityUsage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PreviousUtilityUsage>
 */
class PreviousUtilityUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = PreviousUtilityUsage::class;
    public function definition(): array
    {
        return [
            'month' => $this->faker->dateTimeThisYear,
            'year' => $this->faker->year,
            'water_usage' => $this->faker->randomFloat(2, 0, 100),
            'electricity_usage' => $this->faker->randomFloat(2, 0, 1000),
            'other' => $this->faker->randomFloat(2, 0, 50),
        ];
    }
}
