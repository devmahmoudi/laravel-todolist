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

    public function test_group_has_many_todo()
    {
        $group = Group::factory()->create();
        $todos = Todo::factory()->count(3)->for($group)->create();
        $this->assertCount(3, $group->todo);
        $this->assertTrue($group->todo->contains($todos[0]));
    }

    public function test_fillable_fields()
    {
        $group = new Group([
            'name' => 'Test Group',
            'owner_id' => 1,
        ]);
        $this->assertEquals('Test Group', $group->name);
        $this->assertEquals(1, $group->owner_id);
    }

    public function test_group_owner_scope_returns_only_authenticated_users_groups()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create groups for both users
        $groupsUser1 = Group::factory()->count(2)->create(['owner_id' => $user1->id]);
        Group::factory()->count(2)->create(['owner_id' => $user2->id]);

        // Authenticate as user1
        $this->actingAs($user1);

        // The scope should only return user1's groups
        $groups = Group::all();
        $this->assertCount(2, $groups);
        foreach ($groups as $group) {
            $this->assertEquals($user1->id, $group->owner_id);
        }
    }
} 