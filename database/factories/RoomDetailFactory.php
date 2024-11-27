<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use App\Models\UserDetail;
use App\Models\RoomTypePrice;
use App\Models\UtilityPrice;

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
        $utilityUsage = UtilityUsage::factory()->create();   
        $roomType = RoomTypePrice::factory()->create();
        $utilityPrice = UtilityPrice::factory()->create(); // Create a UtilityPrice instance
        return [
            'floor' => $this->faker->numberBetween(1, 10),
            'user_id' => UserDetail::factory(),
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'available' => $this->faker->boolean,
            'room_code' => $utilityUsage->room_code, // Ensure room_code matches
            'description' => $this->faker->paragraph,
            'room_type_price_id' => $roomType->id, // Use room_type_price_id
            'utility_price_id' => $utilityPrice->id, // Ensure correct field name
        ];
    }
}