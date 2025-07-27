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
            );
    }
}
