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

    /**
     * Pending with a due date still in the future, so it never reads as
     * overdue via Payable::isOverdue().
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'issue_date' => $this->faker->dateTimeBetween('-20 days', '-1 days'),
            'due_date' => $this->faker->dateTimeBetween('+5 days', '+60 days'),
            'payment_date' => null,
            'status' => PayableStatus::Pending,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PayableStatus::Paid,
            'payment_date' => $this->faker->dateTimeBetween($attributes['issue_date'], 'now'),
        ]);
    }

    /**
     * Due date in the past with no payment. In the app this status is
     * only ever computed, never stored (see HasOverdueStatus) — this state
     * exists purely to seed realistic-looking demo/test data.
     */
    public function overdue(): static
    {
        return $this->state(function () {
            $dueDate = $this->faker->dateTimeBetween('-3 months', '-1 days');

            return [
                'issue_date' => (clone $dueDate)->modify('-30 days'),
                'due_date' => $dueDate,
                'payment_date' => null,
                'status' => PayableStatus::Overdue,
            ];
        });
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => PayableStatus::Cancelled,
            'payment_date' => null,
        ]);
    }
}
