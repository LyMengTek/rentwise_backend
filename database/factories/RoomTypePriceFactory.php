<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomTypePrice>
 */
class RoomTypePriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement([1, 2, 3, 4]), // Assuming 1=Studio, 2=1 Bedroom, etc.
            'type_price' => $this->faker->randomFloat(2, 100, 500), // Random price between 100 and 500
        ];
    }
}
