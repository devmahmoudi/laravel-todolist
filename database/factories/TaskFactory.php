<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'parent_id' => null,
            'group_id' => Group::factory(),
        ];
    }

    /**
     * Indicate that the task belongs to a specific group.
     */
    public function forGroup(Group $group): static
    {
        return $this->state(fn (array $attributes) => [
            'group_id' => $group->id,
        ]);
    }

    /**
     * Indicate that the task has a parent task.
     */
    public function withParent(Task $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'group_id' => $parent->group_id,
        ]);
    }

    /**
     * Indicate that the task is a root task (no parent).
     */
    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }
} 