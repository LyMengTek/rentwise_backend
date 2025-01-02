<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\InvoiceDetail;
use App\Models\RentalDetail;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDetail>
 */
class InvoiceDetailFactory extends Factory
{
    protected $model = InvoiceDetail::class;

    public function definition(): array
    {
        // Use an existing RentalDetail record or create a new one if none exist
        $rental = RentalDetail::inRandomOrder()->first() ?? RentalDetail::factory()->create();
        Log::info("Creating InvoiceDetail for rental ID {$rental->id}");

        return [
            'rental_id' => $rental->id,
            'amount_due' => $this->faker->randomFloat(2, 100, 1000),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'paid' => $this->faker->boolean,
        ];
    }
}