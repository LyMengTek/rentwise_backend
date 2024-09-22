<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\InvoiceDetail;
use App\Models\UserDetail;
use App\Models\UtilityUsage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDetail>
 */
class InvoiceDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = InvoiceDetail::class;
    public function definition(): array
    {
        return [
            'user_id' => UserDetail::factory(),
            'usage_id' => UtilityUsage::factory(),
            'amount_due' => $this->faker->randomFloat(2, 100, 1000),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'paid' => $this->faker->boolean,
        ];
    }
}
