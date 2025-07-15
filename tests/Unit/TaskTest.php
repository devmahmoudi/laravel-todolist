<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Task;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_belongs_to_group()
    {
        $group = Group::factory()->create();
        $task = Task::factory()->for($group)->create();
        $this->assertInstanceOf(Group::class, $task->group);
        $this->assertEquals($group->id, $task->group->id);
    }

    public function test_task_can_have_parent_and_children()
    {
        $group = Group::factory()->create();
        $parent = Task::factory()->for($group)->create();
        $child1 = Task::factory()->for($group)->create(['parent_id' => $parent->id]);
        $child2 = Task::factory()->for($group)->create(['parent_id' => $parent->id]);
        $this->assertEquals($parent->id, $child1->parent->id);
        $this->assertEquals($parent->id, $child2->parent->id);
        $this->assertCount(2, $parent->children);
    }

    public function test_fillable_fields()
    {
        $task = new Task([
            'title' => 'Test Task',
            'description' => 'A test task',
            'parent_id' => 1,
            'group_id' => 2,
        ]);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('A test task', $task->description);
        $this->assertEquals(1, $task->parent_id);
        $this->assertEquals(2, $task->group_id);
    }
} 