<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use App\Models\UserDetail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomDetail>
 */
class RoomDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = RoomDetail::class;
    public function definition(): array
    {
        return [
            'floor' => $this->faker->numberBetween(1, 10),
            'user_id' => UserDetail::factory(),
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'available' => $this->faker->boolean,
            'room_price' => $this->faker->randomFloat(2, 100, 1000),
            'water_price' => $this->faker->randomFloat(2, 10, 100),
            'electricity_price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
