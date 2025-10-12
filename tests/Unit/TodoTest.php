<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Todo;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_todo_belongs_to_group()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $group = Group::factory()->for($user, 'owner')->create();
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

    public function test_todo_with_no_ancestors_returns_empty_collection()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->create(['parent_id' => null]);

        $ancestors = $todo->ancestors();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $ancestors);
        $this->assertCount(0, $ancestors);
    }

    public function test_todo_with_single_ancestor_returns_correct_ancestor()
    {
        $group = Group::factory()->create();
        $parent = Todo::factory()->for($group)->create(['title' => 'Parent Todo']);
        $child = Todo::factory()->for($group)->create([
            'parent_id' => $parent->id,
            'title' => 'Child Todo'
        ]);

        $ancestors = $child->ancestors();

        $this->assertCount(1, $ancestors);
        $this->assertEquals($parent->id, $ancestors->first()->id);
        $this->assertEquals('Parent Todo', $ancestors->first()->title);
    }

    public function test_todo_with_multiple_ancestors_returns_ancestors_in_correct_order()
    {
        $group = Group::factory()->create();

        // Create a hierarchy: grandparent -> parent -> child
        $grandparent = Todo::factory()->for($group)->create(['title' => 'Grandparent Todo']);
        $parent = Todo::factory()->for($group)->create([
            'parent_id' => $grandparent->id,
            'title' => 'Parent Todo'
        ]);
        $child = Todo::factory()->for($group)->create([
            'parent_id' => $parent->id,
            'title' => 'Child Todo'
        ]);

        $ancestors = $child->ancestors();

        $this->assertCount(2, $ancestors);

        // First ancestor should be the grandparent (root)
        $this->assertEquals($grandparent->id, $ancestors->first()->id);
        $this->assertEquals('Grandparent Todo', $ancestors->first()->title);

        // Second ancestor should be the parent
        $this->assertEquals($parent->id, $ancestors->last()->id);
        $this->assertEquals('Parent Todo', $ancestors->last()->title);
    }

    public function test_todo_with_deep_hierarchy_returns_all_ancestors()
    {
        $group = Group::factory()->create();

        // Create a deep hierarchy: level1 -> level2 -> level3 -> level4 -> level5
        $level1 = Todo::factory()->for($group)->create(['title' => 'Level 1']);
        $level2 = Todo::factory()->for($group)->create(['parent_id' => $level1->id, 'title' => 'Level 2']);
        $level3 = Todo::factory()->for($group)->create(['parent_id' => $level2->id, 'title' => 'Level 3']);
        $level4 = Todo::factory()->for($group)->create(['parent_id' => $level3->id, 'title' => 'Level 4']);
        $level5 = Todo::factory()->for($group)->create(['parent_id' => $level4->id, 'title' => 'Level 5']);

        $ancestors = $level5->ancestors();

        $this->assertCount(4, $ancestors);

        // Check order: root to immediate parent
        $this->assertEquals($level1->id, $ancestors->get(0)->id);
        $this->assertEquals($level2->id, $ancestors->get(1)->id);
        $this->assertEquals($level3->id, $ancestors->get(2)->id);
        $this->assertEquals($level4->id, $ancestors->get(3)->id);
    }

    public function test_todo_root_returns_itself_if_no_ancestor()
    {
        $group = Group::factory()->create();
        $root = Todo::factory()->for($group)->create(['parent_id' => null, 'title' => 'Root Todo']);
        $this->assertEquals($root->id, $root->root()->id);
        $this->assertEquals('Root Todo', $root->root()->title);
    }

    public function test_todo_root_returns_the_top_ancestor()
    {
        $group = Group::factory()->create();
        // Build a hierarchy: root -> child -> grandchild
        $root = Todo::factory()->for($group)->create(['parent_id' => null, 'title' => 'Root Todo']);
        $child = Todo::factory()->for($group)->create(['parent_id' => $root->id, 'title' => 'Child Todo']);
        $grandchild = Todo::factory()->for($group)->create(['parent_id' => $child->id, 'title' => 'Grandchild Todo']);
        $this->assertEquals($root->id, $grandchild->root()->id);
        $this->assertEquals('Root Todo', $grandchild->root()->title);
    }

    public function test_todo_fillable_fields_includes_completed_at()
    {
        $todo = new Todo([
            'title' => 'Test Todo',
            'description' => 'A test todo',
            'parent_id' => 1,
            'group_id' => 2,
            'completed_at' => now(),
        ]);
        $this->assertEquals('Test Todo', $todo->title);
        $this->assertEquals('A test todo', $todo->description);
        $this->assertEquals(1, $todo->parent_id);
        $this->assertEquals(2, $todo->group_id);
        $this->assertNotNull($todo->completed_at);
    }

    public function test_todo_is_completed_returns_false_when_completed_at_is_null()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->incomplete()->create();

        $this->assertFalse($todo->isCompleted());
        $this->assertNull($todo->completed_at);
    }

    public function test_todo_is_completed_returns_true_when_completed_at_is_set()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->completed()->create();

        $this->assertTrue($todo->isCompleted());
        $this->assertNotNull($todo->completed_at);
    }

    public function test_mark_as_completed_sets_completed_at_timestamp()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->incomplete()->create();

        $this->assertFalse($todo->isCompleted());

        $result = $todo->markAsCompleted();

        $this->assertTrue($result);
        $this->assertTrue($todo->fresh()->isCompleted());
        $this->assertNotNull($todo->fresh()->completed_at);
    }

    public function test_mark_as_incomplete_removes_completed_at_timestamp()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->completed()->create();

        $this->assertTrue($todo->isCompleted());

        $result = $todo->markAsIncomplete();

        $this->assertTrue($result);
        $this->assertFalse($todo->fresh()->isCompleted());
        $this->assertNull($todo->fresh()->completed_at);
    }

    public function test_toggle_completion_changes_status_from_incomplete_to_completed()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->incomplete()->create();

        $this->assertFalse($todo->isCompleted());

        $result = $todo->toggleCompletion();

        $this->assertTrue($result);
        $this->assertTrue($todo->fresh()->isCompleted());
    }

    public function test_toggle_completion_changes_status_from_completed_to_incomplete()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->completed()->create();

        $this->assertTrue($todo->isCompleted());

        $result = $todo->toggleCompletion();

        $this->assertTrue($result);
        $this->assertFalse($todo->fresh()->isCompleted());
    }

    public function test_factory_can_create_completed_todos()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->completed()->create();

        $this->assertTrue($todo->isCompleted());
        $this->assertNotNull($todo->completed_at);
    }

    public function test_factory_can_create_incomplete_todos()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->incomplete()->create();

        $this->assertFalse($todo->isCompleted());
        $this->assertNull($todo->completed_at);
    }
}
