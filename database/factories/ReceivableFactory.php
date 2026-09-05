<?php

namespace Database\Factories;

use App\Enums\ReceivableStatus;
use App\Models\Party;
use App\Models\Receivable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Receivable>
 */
class ReceivableFactory extends Factory
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
            'receipt_date' => null,
            'status' => ReceivableStatus::Pending,
        ];
    }

    /**
     * Pending with a due date still in the future, so it never reads as
     * overdue via Receivable::isOverdue().
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'issue_date' => $this->faker->dateTimeBetween('-20 days', '-1 days'),
            'due_date' => $this->faker->dateTimeBetween('+5 days', '+60 days'),
            'receipt_date' => null,
            'status' => ReceivableStatus::Pending,
        ]);
    }

    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReceivableStatus::Received,
            'receipt_date' => $this->faker->dateTimeBetween($attributes['issue_date'], 'now'),
        ]);
    }

    /**
     * Due date in the past with no receipt. In the app this status is
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
                'receipt_date' => null,
                'status' => ReceivableStatus::Overdue,
            ];
        });
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => ReceivableStatus::Cancelled,
            'receipt_date' => null,
        ]);
    }
}
