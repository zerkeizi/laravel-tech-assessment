<?php

namespace Database\Factories;

use App\Enums\PartyType;
use App\Models\Party;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Party>
 */
class PartyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => PartyType::Individual,
            'name' => $this->faker->name(),
            'document' => $this->faker->numerify('###########'),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->numerify('###########'),
            'active' => true,
        ];
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type' => PartyType::Company,
            'name' => $this->faker->company(),
            'document' => $this->faker->numerify('##############'),
        ]);
    }
}
