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

        $response = $this->post('/group', [
            'name' => 'Test Group',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast@success', 'New group has been created.');
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
} 