<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDetail;
use App\Models\LandlordFloorRoom;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LandlordFloorRoom>
 */
class LandlordFloorRoomFactory extends Factory
{

    protected $model = LandlordFloorRoom::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'landlord_id' => UserDetail::factory(),  // Assumes UserDetail factory exists and generates landlords
            'floor' => $this->faker->numberBetween(1, 10),
            'total_rooms' => $this->faker->numberBetween(1, 20),
        ];
    }
}
