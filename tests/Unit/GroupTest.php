<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Group;
use App\Models\User;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_belongs_to_owner()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $this->assertInstanceOf(User::class, $group->owner);
        $this->assertEquals($user->id, $group->owner->id);
    }

    public function test_group_has_many_tasks()
    {
        $group = Group::factory()->create();
        $todos = Todo::factory()->count(3)->for($group)->create();
        $this->assertCount(3, $group->tasks);
        $this->assertTrue($group->tasks->contains($todos[0]));
    }

    public function test_fillable_fields()
    {
        $group = new Group([
            'name' => 'Test Group',
            'owner_id' => 1,
        ]);
        $this->assertEquals('Test Group', $group->name);
        $this->assertEquals('A test group', $group->desc);
        $this->assertEquals(1, $group->owner_id);
    }
} 