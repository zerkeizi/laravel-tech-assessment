<?php

namespace Database\Factories;

use App\Enums\PayableStatus;
use App\Models\Party;
use App\Models\Payable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payable>
 */
class PayableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-2 months', 'now');

        return [
            'party_id' => Party::factory(),
            'description' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 10, 5000),
            'issue_date' => $issueDate,
            'due_date' => (clone $issueDate)->modify('+30 days'),
            'payment_date' => null,
            'status' => PayableStatus::Pending,
        ];
    }
}
