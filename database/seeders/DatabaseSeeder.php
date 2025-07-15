<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make("123456789")
        ]);

        // Seed Groups and Tasks
        \App\Models\User::factory(5)->create()->each(function ($user) {
            $groups = \App\Models\Group::factory(2)->create(['owner_id' => $user->id]);
            $groups->each(function ($group) {
                // Each group gets 3 tasks
                \App\Models\Task::factory(3)->create(['group_id' => $group->id]);
            });
        });
    }
}
