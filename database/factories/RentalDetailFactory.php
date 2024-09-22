<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RentalDetail;
use App\Models\UserDetail;
use App\Models\RoomDetail;
use App\Models\InvoiceDetail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalDetail>
 */
class RentalDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = RentalDetail::class;
    public function definition(): array
    {
        return [
            'user_id' => UserDetail::factory(),
            'room_id' => RoomDetail::factory(),
            'invoice_id' => InvoiceDetail::factory(),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_active' => $this->faker->boolean,
        ];
    }
}
