<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UtilityUsage;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UtilityUsage>
 */
class UtilityUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = UtilityUsage::class;
    public function definition(): array
    {
        return [
            'water_usage' => $this->faker->randomFloat(2, 0, 100),
            'electricity_usage' => $this->faker->randomFloat(2, 0, 1000),
            'other' => $this->faker->randomFloat(2, 0, 50),
            'room_code' => $this->faker->unique()->numberBetween(100000, 999999),
        ];
    }
}
