<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LandlordDetail;
use App\Models\UserDetail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LandlordDetail>
 */
class LandlordDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = LandlordDetail::class;
    public function definition(): array
    {
        return [
            'user_id' => UserDetail::factory()->state(['user_type' => 'landlord']),
            'join_code' => $this->faker->unique()->numberBetween(100000, 999999),
        ];
    }
}
