<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomDetail;
use App\Models\LandlordDetail;
use App\Models\UtilityUsage;

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
            'landlord_id' => LandlordDetail::factory(),
            'utility_id' => UtilityUsage::factory(),
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'available' => $this->faker->boolean,
            'room_price' => $this->faker->randomFloat(2, 100, 1000)
        ];
    }
}