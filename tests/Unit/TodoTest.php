<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Todo;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_todo_belongs_to_group()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->create();
        $this->assertInstanceOf(Group::class, $todo->group);
        $this->assertEquals($group->id, $todo->group->id);
    }

    public function test_todo_can_have_parent_and_children()
    {
        $group = Group::factory()->create();
        $parent = Todo::factory()->for($group)->create();
        $child1 = Todo::factory()->for($group)->create(['parent_id' => $parent->id]);
        $child2 = Todo::factory()->for($group)->create(['parent_id' => $parent->id]);
        $this->assertEquals($parent->id, $child1->parent->id);
        $this->assertEquals($parent->id, $child2->parent->id);
        $this->assertCount(2, $parent->children);
    }

    public function test_fillable_fields()
    {
        $todo = new Todo([
            'title' => 'Test Todo',
            'description' => 'A test todo',
            'parent_id' => 1,
            'group_id' => 2,
        ]);
        $this->assertEquals('Test Todo', $todo->title);
        $this->assertEquals('A test todo', $todo->description);
        $this->assertEquals(1, $todo->parent_id);
        $this->assertEquals(2, $todo->group_id);
    }
} 