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
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = RoomDetail::class;
    public function definition(): array
    {
        $utilityUsage = UtilityUsage::factory()->create();   
        $roomType = RoomTypePrice::factory()->create();
        return [
            'floor' => $this->faker->numberBetween(1, 10),
            'user_id' => UserDetail::factory(),
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'available' => $this->faker->boolean,
            'water_price' => $this->faker->randomFloat(2, 10, 100),
            'electricity_price' => $this->faker->randomFloat(2, 10, 100),
            'room_code' => $utilityUsage->room_code, // Ensure room_code matches
            'description' => $this->faker->paragraph,
            'room_type' => $roomType->id,
            
        ];
    }
}
