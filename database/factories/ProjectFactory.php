<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', '+1 month');

        return [
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween($startDate, '+1 year')->format('Y-m-d'),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the project is not active.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the project has no end date yet.
     */
    public function withoutEndDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => null,
        ]);
    }
}
