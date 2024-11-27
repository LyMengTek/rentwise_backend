<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomTypePrice;
use App\Models\UserDetail;

class RoomTypePriceFactory extends Factory
{
    protected $model = RoomTypePrice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->numberBetween(1, 5),
            'type_price' => $this->faker->randomFloat(2, 100, 500),
            'landlord_id' => UserDetail::factory()->create()->id, // Use UserDetail factory for landlord_id
        ];
    }
}