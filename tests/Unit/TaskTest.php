<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_be_created(): void
    {
        $group = Group::factory()->create();
        $task = Task::factory()->forGroup($group)->create();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'group_id' => $group->id,
            'parent_id' => null,
        ]);
    }

    public function test_task_belongs_to_group(): void
    {
        $group = Group::factory()->create();
        $task = Task::factory()->forGroup($group)->create();

        $this->assertInstanceOf(Group::class, $task->group);
        $this->assertEquals($group->id, $task->group->id);
    }

    public function test_task_can_have_parent(): void
    {
        $group = Group::factory()->create();
        $parentTask = Task::factory()->forGroup($group)->create();
        $childTask = Task::factory()->forGroup($group)->withParent($parentTask)->create();

        $this->assertInstanceOf(Task::class, $childTask->parent);
        $this->assertEquals($parentTask->id, $childTask->parent->id);
    }

    public function test_task_can_have_children(): void
    {
        $group = Group::factory()->create();
        $parentTask = Task::factory()->forGroup($group)->create();
        $childTasks = Task::factory(3)->forGroup($group)->withParent($parentTask)->create();

        $this->assertCount(3, $parentTask->children);
        $this->assertInstanceOf(Task::class, $parentTask->children->first());
    }

    public function test_task_can_be_root(): void
    {
        $group = Group::factory()->create();
        $rootTask = Task::factory()->forGroup($group)->root()->create();

        $this->assertNull($rootTask->parent_id);
        $this->assertNull($rootTask->parent);
    }

    public function test_task_can_be_filled_with_mass_assignment(): void
    {
        $group = Group::factory()->create();
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'group_id' => $group->id,
            'parent_id' => null,
        ];

        $task = Task::create($taskData);

        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test Description', $task->description);
        $this->assertEquals($group->id, $task->group_id);
        $this->assertNull($task->parent_id);
    }

    public function test_task_has_timestamps(): void
    {
        $task = Task::factory()->create();

        $this->assertNotNull($task->created_at);
        $this->assertNotNull($task->updated_at);
    }

    public function test_task_can_be_updated(): void
    {
        $task = Task::factory()->create();
        $originalTitle = $task->title;
        $newTitle = 'Updated Task Title';

        $task->update(['title' => $newTitle]);

        $this->assertEquals($newTitle, $task->fresh()->title);
        $this->assertNotEquals($originalTitle, $task->fresh()->title);
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::factory()->create();
        $taskId = $task->id;

        $task->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    public function test_task_deletion_cascades_to_children(): void
    {
        $group = Group::factory()->create();
        $parentTask = Task::factory()->forGroup($group)->create();
        $childTasks = Task::factory(3)->forGroup($group)->withParent($parentTask)->create();
        $childTaskIds = $childTasks->pluck('id')->toArray();

        $parentTask->delete();

        foreach ($childTaskIds as $childTaskId) {
            $this->assertDatabaseMissing('tasks', ['id' => $childTaskId]);
        }
    }

    public function test_task_factory_creates_valid_data(): void
    {
        $task = Task::factory()->create();

        $this->assertNotEmpty($task->title);
        $this->assertNotNull($task->group_id);
        $this->assertDatabaseHas('groups', ['id' => $task->group_id]);
    }

    public function test_task_factory_for_group_method(): void
    {
        $group = Group::factory()->create();
        $task = Task::factory()->forGroup($group)->create();

        $this->assertEquals($group->id, $task->group_id);
    }

    public function test_task_factory_with_parent_method(): void
    {
        $group = Group::factory()->create();
        $parentTask = Task::factory()->forGroup($group)->create();
        $childTask = Task::factory()->withParent($parentTask)->create();

        $this->assertEquals($parentTask->id, $childTask->parent_id);
        $this->assertEquals($parentTask->group_id, $childTask->group_id);
    }

    public function test_task_factory_root_method(): void
    {
        $task = Task::factory()->root()->create();

        $this->assertNull($task->parent_id);
    }

    public function test_task_description_can_be_null(): void
    {
        $task = Task::factory()->create([
            'description' => null,
        ]);

        $this->assertNull($task->description);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'description' => null,
        ]);
    }

    public function test_task_parent_id_can_be_null(): void
    {
        $task = Task::factory()->create([
            'parent_id' => null,
        ]);

        $this->assertNull($task->parent_id);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'parent_id' => null,
        ]);
    }

    public function test_task_group_id_can_be_null(): void
    {
        $task = Task::factory()->create([
            'group_id' => null,
        ]);

        $this->assertNull($task->group_id);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'group_id' => null,
        ]);
    }

    public function test_task_hierarchical_relationships(): void
    {
        $group = Group::factory()->create();
        
        // Create a 3-level hierarchy
        $rootTask = Task::factory()->forGroup($group)->root()->create();
        $level1Task = Task::factory()->forGroup($group)->withParent($rootTask)->create();
        $level2Task = Task::factory()->forGroup($group)->withParent($level1Task)->create();

        // Test parent relationships
        $this->assertNull($rootTask->parent_id);
        $this->assertEquals($rootTask->id, $level1Task->parent_id);
        $this->assertEquals($level1Task->id, $level2Task->parent_id);

        // Test children relationships
        $this->assertCount(1, $rootTask->children);
        $this->assertCount(1, $level1Task->children);
        $this->assertCount(0, $level2Task->children);

        // Test descendants
        $this->assertCount(2, $rootTask->getAllDescendants());
        $this->assertCount(1, $level1Task->getAllDescendants());
        $this->assertCount(0, $level2Task->getAllDescendants());

        // Test ancestors
        $this->assertCount(0, $rootTask->getAllAncestors());
        $this->assertCount(1, $level1Task->getAllAncestors());
        $this->assertCount(2, $level2Task->getAllAncestors());
        
        // Test direct parent relationships
        $this->assertNull($rootTask->parent);
        $this->assertEquals($rootTask->id, $level1Task->parent->id);
        $this->assertEquals($level1Task->id, $level2Task->parent->id);
    }
} 