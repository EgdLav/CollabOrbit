<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_workspace(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $data = [
            'name' => 'My Workspace',
            'description' => 'Test workspace description',
            'slug' => 'my-workspace',
        ];

        $response = $this->postJson('/api/workspaces', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.workspace.slug', 'my-workspace');

        $this->assertDatabaseHas('workspaces', [
            'name' => 'My Workspace',
            'slug' => 'my-workspace',
            'owner_id' => $user->id,
        ]);
    }

    public function test_workspace_create_requires_slug(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/workspaces', [
            'name' => 'My Workspace',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_update_workspace(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'old-workspace',
        ]);
        $workspace->users()->attach($user->id);
        $this->actingAs($user, 'sanctum');

        $updateData = [
            'name' => 'Updated Workspace',
            'description' => 'Updated description',
            'slug' => 'updated-workspace',
        ];

        $response = $this->patchJson("/api/workspaces/{$workspace->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Workspace',
                'description' => 'Updated description',
                'slug' => 'updated-workspace',
            ]);

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
            'name' => 'Updated Workspace',
            'slug' => 'updated-workspace',
        ]);
    }

    public function test_non_owner_cannot_update_workspace(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'owner-workspace',
        ]);
        $workspace->users()->attach([$owner->id, $user->id]);
        $this->actingAs($user, 'sanctum');

        $response = $this->patchJson("/api/workspaces/{$workspace->id}", [
            'name' => 'Updated by member',
            'slug' => 'updated-by-member',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_workspace(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'delete-workspace',
        ]);
        $workspace->users()->attach($user->id);
        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/workspaces/{$workspace->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('workspaces', [
            'id' => $workspace->id,
        ]);
    }
}
