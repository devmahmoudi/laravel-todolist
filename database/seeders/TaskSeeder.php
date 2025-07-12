<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all groups
        $groups = Group::all();

        if ($groups->isEmpty()) {
            // Create groups if they don't exist
            $this->call(GroupSeeder::class);
            $groups = Group::all();
        }

        foreach ($groups as $group) {
            // Create root tasks for each group
            $rootTasks = Task::factory(rand(2, 5))
                ->forGroup($group)
                ->root()
                ->create();

            // Create child tasks for some root tasks
            foreach ($rootTasks->take(rand(1, 3)) as $rootTask) {
                Task::factory(rand(1, 3))
                    ->forGroup($group)
                    ->withParent($rootTask)
                    ->create();
            }
        }

        // Create some additional standalone tasks
        Task::factory(20)->create();
    }
} 