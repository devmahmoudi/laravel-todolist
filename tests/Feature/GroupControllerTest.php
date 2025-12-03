<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_group()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/group', [
            'name' => 'Test Group',
        ]);

        $response->assertCreated();
        $response->assertJson([
            'message' => 'New group has been created.',
        ]);
        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group',
            'owner_id' => $user->id,
        ]);
    }

    public function test_group_name_is_required()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/group', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_group_name_must_be_unique()
    {
        $user = User::factory()->create();
        Group::factory()->for($user, 'owner')->create(['name' => 'Existing Group']);
        $this->actingAs($user);

        $response = $this->post('/group', [
            'name' => 'Existing Group',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_authenticated_user_can_update_group()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create(['name' => 'Old Name']);
        $this->actingAs($user);

        $response = $this->patchJson('/group/' . $group->id, [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Group has been updated.',
        ]);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'New Name',
        ]);
    }

    public function test_group_update_name_is_required()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create(['name' => 'Old Name']);
        $this->actingAs($user);

        $response = $this->patch('/group/' . $group->id, [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_group_update_name_must_be_unique()
    {
        $user = User::factory()->create();
        $group1 = Group::factory()->for($user, 'owner')->create(['name' => 'Group One']);
        $group2 = Group::factory()->for($user, 'owner')->create(['name' => 'Group Two']);
        $this->actingAs($user);

        $response = $this->patch('/group/' . $group2->id, [
            'name' => 'Group One',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_user_cannot_update_another_users_group()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $group = Group::factory()->for($user2, 'owner')->create(['name' => 'Other Group']);
        $this->actingAs($user1);

        $response = $this->patch('/group/' . $group->id, [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Other Group',
        ]);
    }

    public function test_authenticated_user_can_delete_group()
    {
        $user = User::factory()->create();
        $group = Group::factory()->for($user, 'owner')->create();
        $this->actingAs($user);

        $response = $this->deleteJson('/group/' . $group->id);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Group has been deleted.',
        ]);
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }

    public function test_non_owner_cannot_delete_group()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $group = Group::factory()->for($owner, 'owner')->create();
        $this->actingAs($otherUser);

        $response = $this->delete('/group/' . $group->id);

        $response->assertStatus(404);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }
} 