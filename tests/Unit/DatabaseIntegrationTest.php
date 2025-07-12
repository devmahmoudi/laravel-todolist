<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_workflow_with_all_models(): void
    {
        // Create a user
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // Create groups for the user
        $groups = Group::factory(2)->forUser($user)->create();
        $this->assertCount(2, $user->groups);

        // Create tasks for each group
        foreach ($groups as $group) {
            $tasks = Task::factory(3)->forGroup($group)->create();
            $this->assertCount(3, $group->tasks);
        }

        // Verify all relationships
        $this->assertCount(6, Task::all());
        $this->assertCount(2, Group::all());
        $this->assertCount(1, User::all());
    }

    public function test_hierarchical_task_structure(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();

        // Create a hierarchical task structure
        $rootTask = Task::factory()->forGroup($group)->root()->create();
        $level1Task1 = Task::factory()->forGroup($group)->withParent($rootTask)->create();
        $level1Task2 = Task::factory()->forGroup($group)->withParent($rootTask)->create();
        $level2Task = Task::factory()->forGroup($group)->withParent($level1Task1)->create();

        // Test the hierarchy
        $this->assertCount(2, $rootTask->children);
        $this->assertCount(1, $level1Task1->children);
        $this->assertCount(0, $level1Task2->children);
        $this->assertCount(0, $level2Task->children);

        // Test descendants
        $this->assertCount(3, $rootTask->getAllDescendants());
        $this->assertCount(1, $level1Task1->getAllDescendants());
        $this->assertCount(0, $level1Task2->getAllDescendants());
        $this->assertCount(0, $level2Task->getAllDescendants());

        // Test ancestors
        $this->assertCount(0, $rootTask->getAllAncestors());
        $this->assertCount(1, $level1Task1->getAllAncestors());
        $this->assertCount(1, $level1Task2->getAllAncestors());
        $this->assertCount(2, $level2Task->getAllAncestors());
        
        // Test direct parent relationships
        $this->assertNull($rootTask->parent);
        $this->assertEquals($rootTask->id, $level1Task1->parent->id);
        $this->assertEquals($rootTask->id, $level1Task2->parent->id);
        $this->assertEquals($level1Task1->id, $level2Task->parent->id);
    }

    public function test_cascade_deletions(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();
        $rootTask = Task::factory()->forGroup($group)->create();
        $childTask = Task::factory()->forGroup($group)->withParent($rootTask)->create();

        // Delete user - should cascade to groups and tasks
        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $rootTask->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $childTask->id]);
    }

    public function test_task_cascade_deletion(): void
    {
        $group = Group::factory()->create();
        $rootTask = Task::factory()->forGroup($group)->create();
        $childTask = Task::factory()->forGroup($group)->withParent($rootTask)->create();

        // Delete root task - should cascade to child
        $rootTask->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $rootTask->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $childTask->id]);
        $this->assertDatabaseHas('groups', ['id' => $group->id]);
    }

    public function test_group_cascade_deletion(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();
        $task = Task::factory()->forGroup($group)->create();

        // Delete group - should cascade to tasks
        $group->delete();

        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_foreign_key_constraints(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();
        $task = Task::factory()->forGroup($group)->create();

        // Verify foreign key relationships
        $this->assertEquals($user->id, $group->owner_id);
        $this->assertEquals($group->id, $task->group_id);

        // Verify the relationships work
        $this->assertEquals($user->id, $task->group->owner->id);
    }

    public function test_nullable_fields(): void
    {
        // Test that nullable fields work correctly
        $task = Task::factory()->create([
            'description' => null,
            'parent_id' => null,
            'group_id' => null,
        ]);

        $this->assertNull($task->description);
        $this->assertNull($task->parent_id);
        $this->assertNull($task->group_id);
        $this->assertNull($task->parent);
        $this->assertNull($task->group);
    }

    public function test_factory_methods_work_correctly(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();
        $parentTask = Task::factory()->forGroup($group)->root()->create();
        $childTask = Task::factory()->forGroup($group)->withParent($parentTask)->create();

        // Test factory methods
        $this->assertEquals($user->id, $group->owner_id);
        $this->assertEquals($group->id, $parentTask->group_id);
        $this->assertNull($parentTask->parent_id);
        $this->assertEquals($parentTask->id, $childTask->parent_id);
        $this->assertEquals($group->id, $childTask->group_id);
    }

    public function test_seeder_integration(): void
    {
        // Run the seeders
        $this->artisan('db:seed');

        // Verify data was created
        $this->assertGreaterThan(0, User::count());
        $this->assertGreaterThan(0, Group::count());
        $this->assertGreaterThan(0, Task::count());

        // Verify relationships are intact
        $groups = Group::all();
        foreach ($groups as $group) {
            $this->assertNotNull($group->owner);
            $this->assertDatabaseHas('users', ['id' => $group->owner_id]);
        }

        $tasks = Task::all();
        foreach ($tasks as $task) {
            if ($task->group_id) {
                $this->assertNotNull($task->group);
                $this->assertDatabaseHas('groups', ['id' => $task->group_id]);
            }
            if ($task->parent_id) {
                $this->assertNotNull($task->parent);
                $this->assertDatabaseHas('tasks', ['id' => $task->parent_id]);
            }
        }
    }
} 