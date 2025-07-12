<?php

namespace Tests\Unit;

use App\Models\Group;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_can_be_created(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => $group->name,
            'desc' => $group->desc,
            'owner_id' => $user->id,
        ]);
    }

    public function test_group_belongs_to_owner(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();

        $this->assertInstanceOf(User::class, $group->owner);
        $this->assertEquals($user->id, $group->owner->id);
    }

    public function test_group_has_many_tasks(): void
    {
        $group = Group::factory()->create();
        $tasks = Task::factory(3)->forGroup($group)->create();

        $this->assertCount(3, $group->tasks);
        $this->assertInstanceOf(Task::class, $group->tasks->first());
    }

    public function test_group_can_be_filled_with_mass_assignment(): void
    {
        $user = User::factory()->create();
        $groupData = [
            'name' => 'Test Group',
            'desc' => 'Test Description',
            'owner_id' => $user->id,
        ];

        $group = Group::create($groupData);

        $this->assertEquals('Test Group', $group->name);
        $this->assertEquals('Test Description', $group->desc);
        $this->assertEquals($user->id, $group->owner_id);
    }

    public function test_group_has_timestamps(): void
    {
        $group = Group::factory()->create();

        $this->assertNotNull($group->created_at);
        $this->assertNotNull($group->updated_at);
    }

    public function test_group_can_be_updated(): void
    {
        $group = Group::factory()->create();
        $originalName = $group->name;
        $newName = 'Updated Group Name';

        $group->update(['name' => $newName]);

        $this->assertEquals($newName, $group->fresh()->name);
        $this->assertNotEquals($originalName, $group->fresh()->name);
    }

    public function test_group_can_be_deleted(): void
    {
        $group = Group::factory()->create();
        $groupId = $group->id;

        $group->delete();

        $this->assertDatabaseMissing('groups', ['id' => $groupId]);
    }

    public function test_group_deletion_cascades_to_tasks(): void
    {
        $group = Group::factory()->create();
        $tasks = Task::factory(3)->forGroup($group)->create();
        $taskIds = $tasks->pluck('id')->toArray();

        $group->delete();

        foreach ($taskIds as $taskId) {
            $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
        }
    }

    public function test_group_factory_creates_valid_data(): void
    {
        $group = Group::factory()->create();

        $this->assertNotEmpty($group->name);
        $this->assertNotEmpty($group->desc);
        $this->assertNotNull($group->owner_id);
        $this->assertDatabaseHas('users', ['id' => $group->owner_id]);
    }

    public function test_group_factory_for_user_method(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->forUser($user)->create();

        $this->assertEquals($user->id, $group->owner_id);
    }
} 