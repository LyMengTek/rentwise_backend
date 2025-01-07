<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RentalDetail;
use App\Models\UserDetail;
use App\Models\RoomDetail;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalDetail>
 */
class RentalDetailFactory extends Factory
{
    protected $model = RentalDetail::class;

    public function definition(): array
    {
        // Use existing UserDetail records or create new ones if none exist
        $landlord = UserDetail::inRandomOrder()->first() ?? UserDetail::factory()->create();
        $renter = UserDetail::inRandomOrder()->first() ?? UserDetail::factory()->create();

        // Use existing RoomDetail records or create new ones if none exist
        $room = RoomDetail::inRandomOrder()->first() ?? RoomDetail::factory()->create(['user_id' => $landlord->id]);

        Log::info("Creating RentalDetail for landlord ID {$landlord->id}, renter ID {$renter->id}, room ID {$room->id}");

        return [
            'landlord_id' => $landlord->id,
            'renter_id' => $renter->id,
            'room_id' => $room->id,
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_active' => $this->faker->boolean,
        ];
    }
    
}