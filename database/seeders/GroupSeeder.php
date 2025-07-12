<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users if they don't exist
        $users = User::factory(5)->create();

        // Create groups for each user
        foreach ($users as $user) {
            Group::factory(rand(1, 3))->forUser($user)->create();
        }

        // Create some additional groups
        Group::factory(10)->create();
    }
} 