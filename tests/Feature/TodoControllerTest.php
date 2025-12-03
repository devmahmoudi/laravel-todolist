<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Todo;
use App\Models\User;
use Database\Factories\GroupFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_render_todo_index_page()
    {
        $user = User::factory()->create();

        $group = Group::factory()->for($user, 'owner')->has(Todo::factory()->count(10))->create();

        $this->actingAs($user);

        $response = $this->getJson(route('group.todo', $group->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'group',
            'data',
        ]);
    }

    public function test_pass_group_and_todo_list_to_todo_index_page()
    {
        $user = User::factory()->create();

        $topLevelTodoCount = rand(1, 5);
        $childTodoCount = rand(2, 5);

        $group = Group::factory()->for($user, 'owner')->create();

        // Create top-level todos
        $topLevelTodos = Todo::factory()->count($topLevelTodoCount)->incomplete()->for($group)->create();

        // Create child todos for the first top-level todo
        Todo::factory()->count($childTodoCount)->incomplete()->for($group)->create([
            'parent_id' => $topLevelTodos->first()->id
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('group.todo', $group->id));

        $response->assertOk();
        $response->assertJsonPath('group.id', $group->id);
        $response->assertJsonCount($topLevelTodoCount, 'data');
        $response->assertJsonStructure([
            'group',
            'data' => [
                [
                    'id',
                    'title',
                    'children',
                ],
            ],
        ]);
    }

    public function test_store_todo_creates_todo_and_redirects_with_success_message()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $this->actingAs($user);

        $data = [
            'title' => 'Test Todo',
            'description' => 'Test description',
            'group_id' => $group->id,
            'parent_id' => null,
        ];

        $response = $this->postJson(route('todo.store'), $data);

        $this->assertDatabaseHas('todos', $data);

        $response->assertCreated();
        $response->assertJson([
            'message' => 'Todo created successfully.',
        ]);
    }

    public function test_destroy_todo_deletes_todo_and_redirects_with_success_message()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();

        $this->actingAs($user);

        $response = $this->deleteJson(route('todo.delete', $todo->id));

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo deleted successfully.',
        ]);
    }

    public function test_update_todo_updates_todo_and_redirects_with_success_message()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Original Title',
            'description' => 'Original description',
        ]);

        $this->actingAs($user);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson(route('todo.update', $todo->id), $updateData);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo updated successfully.',
        ]);
    }

    public function test_update_todo_with_only_title()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Original Title',
            'description' => 'Original description',
        ]);

        $this->actingAs($user);

        $updateData = [
            'title' => 'Updated Title Only',
        ];

        $response = $this->putJson(route('todo.update', $todo->id), $updateData);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title Only',
            'description' => 'Original description', // Should remain unchanged
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo updated successfully.',
        ]);
    }

    public function test_update_todo_with_null_description()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Original Title',
            'description' => 'Original description',
        ]);

        $this->actingAs($user);

        $updateData = [
            'title' => 'Updated Title',
            'description' => null,
        ];

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'description' => null,
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo updated successfully.',
        ]);
    }

    public function test_update_todo_validation_requires_title()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();

        $this->actingAs($user);

        $updateData = [
            'description' => 'Updated description',
        ];

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_update_todo_validation_title_max_length()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();

        $this->actingAs($user);

        $updateData = [
            'title' => str_repeat('a', 256), // Exceeds 255 character limit
            'description' => 'Updated description',
        ];

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_update_todo_validation_title_must_be_string()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();

        $this->actingAs($user);

        $updateData = [
            'title' => 123, // Not a string
            'description' => 'Updated description',
        ];

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_update_todo_validation_description_must_be_string()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();

        $this->actingAs($user);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 123, // Not a string
        ];

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_update_todo_authorization_requires_authenticated_user()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->create();

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ];

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $response->assertRedirect('/login');
    }

    public function test_update_todo_returns_404_for_nonexistent_todo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ];

        $response = $this->put(route('todo.update', 99999), $updateData);

        $response->assertNotFound();
    }

    public function test_show_todo_renders_todo_detail_page()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();

        $this->actingAs($user);

        $response = $this->getJson(route('todo.show', $todo->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'ancestors',
        ]);
    }

    public function test_show_todo_passes_todo_data_to_page()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Test Todo Title',
            'description' => 'Test Todo Description',
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('todo.show', $todo->id));

        $response->assertOk();
        $response->assertJsonPath('data.id', $todo->id);
        $response->assertJsonPath('data.title', 'Test Todo Title');
        $response->assertJsonPath('data.description', 'Test Todo Description');
        $response->assertJsonPath('data.group_id', $group->id);
        $response->assertJsonStructure([
            'data' => [
                'group',
                'children',
            ],
            'ancestors',
        ]);
    }

    public function test_show_todo_requires_authenticated_user()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->create();

        $response = $this->get(route('todo.show', $todo->id));

        $response->assertRedirect('/login');
    }

    public function test_show_todo_returns_404_for_nonexistent_todo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('todo.show', 99999));

        $response->assertNotFound();
    }

    public function test_show_todo_with_null_description()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Todo with no description',
            'description' => null,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('todo.show', $todo->id));

        $response->assertOk();
        $response->assertJsonPath('data.id', $todo->id);
        $response->assertJsonPath('data.title', 'Todo with no description');
        $response->assertJsonPath('data.description', null);
        $response->assertJsonStructure([
            'data' => [
                'children',
            ],
            'ancestors',
        ]);
    }

    public function test_show_todo_includes_ancestors_data()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();

        // Create a hierarchy: grandparent -> parent -> child
        $grandparent = Todo::factory()->for($group)->create([
            'title' => 'Grandparent Todo',
            'description' => 'Grandparent description',
        ]);

        $parent = Todo::factory()->for($group)->create([
            'title' => 'Parent Todo',
            'description' => 'Parent description',
            'parent_id' => $grandparent->id,
        ]);

        $child = Todo::factory()->for($group)->create([
            'title' => 'Child Todo',
            'description' => 'Child description',
            'parent_id' => $parent->id,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('todo.show', $child->id));

        $response->assertOk();
        $response->assertJsonPath('data.id', $child->id);
        $response->assertJsonPath('data.title', 'Child Todo');
        $response->assertJsonCount(2, 'ancestors');
        $response->assertJsonPath('ancestors.0.id', $grandparent->id);
        $response->assertJsonPath('ancestors.0.title', 'Grandparent Todo');
        $response->assertJsonPath('ancestors.1.id', $parent->id);
        $response->assertJsonPath('ancestors.1.title', 'Parent Todo');
    }

    public function test_todo_index_includes_children_in_todos()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();

        // Create parent todo
        $parentTodo = Todo::factory()->for($group)->incomplete()->create([
            'title' => 'Parent Todo',
            'description' => 'Parent description',
        ]);

        // Create child todos
        $childTodo1 = Todo::factory()->for($group)->incomplete()->create([
            'title' => 'Child Todo 1',
            'description' => 'Child description 1',
            'parent_id' => $parentTodo->id,
        ]);

        $childTodo2 = Todo::factory()->for($group)->incomplete()->create([
            'title' => 'Child Todo 2',
            'description' => 'Child description 2',
            'parent_id' => $parentTodo->id,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('group.todo', $group->id));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonCount(2, 'data.0.children');
        $response->assertJsonPath('data.0.id', $parentTodo->id);
        $response->assertJsonPath('data.0.title', 'Parent Todo');
    }

    public function test_show_todo_includes_children_data()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();

        // Create parent todo
        $parentTodo = Todo::factory()->for($group)->create([
            'title' => 'Parent Todo',
            'description' => 'Parent description',
        ]);

        // Create child todos
        $childTodo1 = Todo::factory()->for($group)->create([
            'title' => 'Child Todo 1',
            'description' => 'Child description 1',
            'parent_id' => $parentTodo->id,
        ]);

        $childTodo2 = Todo::factory()->for($group)->create([
            'title' => 'Child Todo 2',
            'description' => 'Child description 2',
            'parent_id' => $parentTodo->id,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('todo.show', $parentTodo->id));

        $response->assertOk();
        $response->assertJsonPath('data.id', $parentTodo->id);
        $response->assertJsonPath('data.title', 'Parent Todo');
        $response->assertJsonPath('data.description', 'Parent description');
        $response->assertJsonCount(2, 'data.children');
        $response->assertJsonPath('data.children.0.id', $childTodo1->id);
        $response->assertJsonPath('data.children.0.title', 'Child Todo 1');
        $response->assertJsonPath('data.children.0.parent_id', $parentTodo->id);
        $response->assertJsonPath('data.children.1.id', $childTodo2->id);
        $response->assertJsonPath('data.children.1.title', 'Child Todo 2');
        $response->assertJsonPath('data.children.1.parent_id', $parentTodo->id);
    }

    public function test_show_todo_with_no_ancestors_returns_empty_ancestors()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Top Level Todo',
            'description' => 'No parent',
            'parent_id' => null,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('todo.show', $todo->id));

        $response->assertOk();
        $response->assertJsonPath('data.id', $todo->id);
        $response->assertJsonPath('data.title', 'Top Level Todo');
        $response->assertJsonCount(0, 'ancestors');
    }

    public function test_toggle_completed_marks_incomplete_todo_as_completed()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Test Todo',
            'completed_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->patchJson(route('todo.toggle-completed', $todo->id));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo marked as completed',
        ]);
    }

    public function test_toggle_completed_marks_completed_todo_as_incomplete()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $completedAt = now()->subDays(1);
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Test Todo',
            'completed_at' => $completedAt,
        ]);

        $this->actingAs($user);

        $response = $this->patchJson(route('todo.toggle-completed', $todo->id));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed_at' => null,
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo marked as incomplete',
        ]);
    }

    public function test_toggle_completed_requires_authenticated_user()
    {
        $group = Group::factory()->create();
        $todo = Todo::factory()->for($group)->create();

        $response = $this->patch(route('todo.toggle-completed', $todo->id));

        $response->assertRedirect('/login');
    }

    public function test_toggle_completed_returns_404_for_nonexistent_todo()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->patch(route('todo.toggle-completed', 99999));

        $response->assertNotFound();
    }

    public function test_toggle_completed_updates_todo_with_current_timestamp()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Test Todo',
            'completed_at' => null,
        ]);

        $this->actingAs($user);

        $beforeToggle = now()->subSecond();
        $response = $this->patchJson(route('todo.toggle-completed', $todo->id));
        $afterToggle = now()->addSecond();

        $todo->refresh();

        $this->assertNotNull($todo->completed_at);
        $this->assertTrue($todo->completed_at->between($beforeToggle, $afterToggle));

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo marked as completed',
        ]);
    }

    public function test_toggle_completed_preserves_other_todo_attributes()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create([
            'title' => 'Test Todo',
            'description' => 'Test Description',
            'completed_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->patchJson(route('todo.toggle-completed', $todo->id));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Test Todo',
            'description' => 'Test Description',
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo marked as completed',
        ]);
    }

    public function test_toggle_completed_works_with_child_todos()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();

        $parentTodo = Todo::factory()->for($group)->create([
            'title' => 'Parent Todo',
            'completed_at' => null,
        ]);

        $childTodo = Todo::factory()->for($group)->create([
            'title' => 'Child Todo',
            'parent_id' => $parentTodo->id,
            'completed_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->patchJson(route('todo.toggle-completed', $childTodo->id));

        $this->assertDatabaseHas('todos', [
            'id' => $childTodo->id,
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);

        // Parent should remain unchanged
        $this->assertDatabaseHas('todos', [
            'id' => $parentTodo->id,
            'completed_at' => null,
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Todo marked as completed',
        ]);
    }
}
