<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomDetail;
use App\Models\UserDetail;
use App\Models\UtilityPrice;
use App\Models\RoomTypePrice;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomDetail>
 */
class RoomDetailFactory extends Factory
{
    protected $model = RoomDetail::class;

    public function definition(): array
    {
        Log::info('Creating UserDetail for room...');
        $user = UserDetail::factory()->create();
        Log::info("UserDetail created: ID {$user->id}");

        Log::info('Creating UtilityPrice for room...');
        $utilityPrice = UtilityPrice::factory()->create();
        Log::info("UtilityPrice created: ID {$utilityPrice->id}");

        // Use existing RoomTypePrice records or create new ones if none exist
        $roomTypePrice = RoomTypePrice::inRandomOrder()->first() ?? RoomTypePrice::factory()->create(['landlord_id' => $user->id]);
        Log::info("RoomTypePrice created: ID {$roomTypePrice->id}");

        return [
            'floor' => $this->faker->numberBetween(1, 10),
            'user_id' => $user->id,
            'room_number' => $this->faker->unique()->numberBetween(100, 999),
            'available' => $this->faker->boolean,
            'room_code' => $this->faker->unique()->numberBetween(100000, 999999),
            'description' => $this->faker->paragraph,
            'room_type_price_id' => $roomTypePrice->id,
            'utility_price_id' => $utilityPrice->id,
        ];
    }
}