<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use App\Models\UserDetail;
use App\Models\RoomTypePrice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomDetail>
 */
class RoomDetailFactory extends Factory
{
    protected $model = RoomDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'floor' => $this->faker->numberBetween(1, 10),
            'user_id' => UserDetail::factory(), // Generate a related UserDetail
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'available' => $this->faker->boolean,
            'room_code' => $this->faker->unique()->numberBetween(1000, 9999), // Generate a unique room code
            'description' => $this->faker->paragraph,
            'utility_price_id' => null, // Set to null if not linking to UtilityPrice
            'room_type_id' => RoomTypePrice::factory(), // Generate a related RoomTypePrice
        ];
    }
}
