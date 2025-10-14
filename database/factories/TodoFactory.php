<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'group_id' => \App\Models\Group::factory(),
            'parent_id' => null, // By default, no parent. Can be set in tests.
            'completed_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'), // 30% chance of being completed
        ];
    }

    /**
     * Indicate that the todo is completed.
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'completed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the todo is incomplete.
     */
    public function incomplete(): static
    {
        return $this->state(fn(array $attributes) => [
            'completed_at' => null,
        ]);
    }
}
