<?php

namespace Tests\Unit;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Group;
use App\Models\Todo;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandleInertiaRequestMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_active_group_returns_group_and_todo_group()
    {
        // Scenario: Route param is a Group
        Route::shouldReceive('getCurrentRoute->parameters')
            ->once()
            ->andReturn([
                new Group(['id' => 1, 'name' => 'Test Group']),
            ]);
        $middleware = new HandleInertiaRequests();
        $group = $middleware->getActiveGroup();
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals('Test Group', $group->name);

        // Scenario: Route param is a Todo (with group relationship)
        $fakeGroup = new Group(['id' => 2, 'name' => 'Other Group']);
        $todo = new Todo(['id' => 3, 'group_id' => 2]);
        $todo->setRelation('group', $fakeGroup);
        Route::shouldReceive('getCurrentRoute->parameters')
            ->once()
            ->andReturn([$todo]);
        $middleware = new HandleInertiaRequests();
        $groupFromTodo = $middleware->getActiveGroup();
        $this->assertInstanceOf(Group::class, $groupFromTodo);
        $this->assertEquals('Other Group', $groupFromTodo->name);

        // Scenario: No Group/Todo in route params
        Route::shouldReceive('getCurrentRoute->parameters')
            ->once()
            ->andReturn([]);
        $middleware = new HandleInertiaRequests();
        $this->assertNull($middleware->getActiveGroup());
    }
}
