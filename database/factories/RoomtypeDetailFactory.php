<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RoomtypeDetail;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomtypeDetail>
 */
class RoomtypeDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = RoomtypeDetail::class;
    public function definition(): array
    {
        return [
            'room_type' => $this->faker->word,
            'room_price' => $this->faker->randomFloat(2, 100, 1000)
        ];
    }
}
