<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDetail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = UserDetail::class;
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'password' => 'password',
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'profile_picture' => $this->faker->imageUrl(),
            'id_card_picture' => $this->faker->imageUrl(),
            'user_type' => $this->faker->randomElement(['renter', 'landlord']),
        ];
    }
}
