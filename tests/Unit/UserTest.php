<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_groups(): void
    {
        $user = User::factory()->create();
        $groups = Group::factory(3)->forUser($user)->create();

        $this->assertCount(3, $user->groups);
        $this->assertInstanceOf(Group::class, $user->groups->first());
    }

    public function test_user_groups_relationship(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();

        $this->assertEquals($user->id, $group->owner_id);
        $this->assertTrue($user->groups->contains($group));
    }

    public function test_user_can_own_multiple_groups(): void
    {
        $user = User::factory()->create();
        $group1 = Group::factory()->forUser($user)->create();
        $group2 = Group::factory()->forUser($user)->create();

        $this->assertCount(2, $user->groups);
        $this->assertTrue($user->groups->contains($group1));
        $this->assertTrue($user->groups->contains($group2));
    }

    public function test_user_deletion_cascades_to_groups(): void
    {
        $user = User::factory()->create();
        $groups = Group::factory(3)->forUser($user)->create();
        $groupIds = $groups->pluck('id')->toArray();

        $user->delete();

        foreach ($groupIds as $groupId) {
            $this->assertDatabaseMissing('groups', ['id' => $groupId]);
        }
    }
} 