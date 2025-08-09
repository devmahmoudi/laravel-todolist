<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Todo;
use App\Models\User;
use Database\Factories\GroupFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_render_todo_index_page()
    {
        $user = User::factory()->create();

        $group = Group::factory()->for($user, 'owner')->has(Todo::factory()->count(10))->create();

        $this->actingAs($user);

        $this->get(route('group.todo', $group->id))
            ->assertInertia(fn (Assert $page) =>
                $page->component('todo/todo-index')
            );
    }
    
    public function test_pass_group_and_todo_list_to_todo_index_page()
    {
        $user = User::factory()->create();

        $todoCount = rand(1, 10);

        $group = Group::factory()->for($user, 'owner')->has(Todo::factory()->count($todoCount))->create();

        $this->actingAs($user);

        $this->get(route('group.todo', $group->id))
            ->assertInertia(fn (Assert $page) =>
                $page->component('todo/todo-index')
                ->has('group', fn (Assert $page) =>
                    $page->where('id', $group->id)
                        ->where('name', $group->name)
                        ->where('owner_id', $group->owner_id)
                        ->etc()
                )
                ->has('todos', $todoCount)
                ->has('todos.0.children')
            );
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

        $response = $this->post(route('todo.store'), $data);

        $this->assertDatabaseHas('todos', $data);

        $response->assertRedirect();
        $response->assertSessionHas('toast.success', 'Todo created successfully.');
    }

    public function test_destroy_todo_deletes_todo_and_redirects_with_success_message()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $todo = Todo::factory()->for($group)->create();
        
        $this->actingAs($user);

        $response = $this->delete(route('todo.delete', $todo->id));

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);

        $response->assertRedirect();
        $response->assertSessionHas('toast.success', 'Todo deleted successfully.');
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

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast.success', 'Todo updated successfully.');
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

        $response = $this->put(route('todo.update', $todo->id), $updateData);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Updated Title Only',
            'description' => 'Original description', // Should remain unchanged
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast.success', 'Todo updated successfully.');
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

        $response->assertRedirect();
        $response->assertSessionHas('toast.success', 'Todo updated successfully.');
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

        $response = $this->get(route('todo.show', $todo->id));

        $response->assertInertia(fn (Assert $page) =>
            $page->component('todo/todo-detail')
        );
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

        $response = $this->get(route('todo.show', $todo->id));

        $response->assertInertia(fn (Assert $page) =>
            $page->component('todo/todo-detail')
                ->has('todo', fn (Assert $page) =>
                    $page->where('id', $todo->id)
                        ->where('title', 'Test Todo Title')
                        ->where('description', 'Test Todo Description')
                        ->where('group_id', $group->id)
                        ->has('children')
                        ->etc()
                )
        );
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

        $response = $this->get(route('todo.show', $todo->id));

        $response->assertInertia(fn (Assert $page) =>
            $page->component('todo/todo-detail')
                ->has('todo', fn (Assert $page) =>
                    $page->where('id', $todo->id)
                        ->where('title', 'Todo with no description')
                        ->where('description', null)
                        ->has('children')
                        ->etc()
                )
        );
    }

    public function test_todo_index_includes_children_in_todos()
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

        $this->get(route('group.todo', $group->id))
            ->assertInertia(fn (Assert $page) =>
                $page->component('todo/todo-index')
                ->has('todos', 3) // Parent + 2 children
                ->has('todos.0.children')
            );
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

        $response = $this->get(route('todo.show', $parentTodo->id));

        $response->assertInertia(fn (Assert $page) =>
            $page->component('todo/todo-detail')
                ->has('todo', fn (Assert $page) =>
                    $page->where('id', $parentTodo->id)
                        ->where('title', 'Parent Todo')
                        ->where('description', 'Parent description')
                        ->has('children', 2)
                        ->has('children.0', fn (Assert $page) =>
                            $page->where('id', $childTodo1->id)
                                ->where('title', 'Child Todo 1')
                                ->where('parent_id', $parentTodo->id)
                                ->etc()
                        )
                        ->has('children.1', fn (Assert $page) =>
                            $page->where('id', $childTodo2->id)
                                ->where('title', 'Child Todo 2')
                                ->where('parent_id', $parentTodo->id)
                                ->etc()
                        )
                        ->etc()
                )
        );
    }
}
