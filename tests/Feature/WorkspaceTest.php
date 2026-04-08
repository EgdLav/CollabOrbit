<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_user_can_create_workspace(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $data = [
            'name' => 'My Workspace',
            'description' => 'Test workspace description',
        ];

        $response = $this->postJson('/api/workspaces', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'workspace' => [
                        'id',
                        'name',
                        'description',
                        'owner_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('workspaces', [
            'name' => 'My Workspace',
            'owner_id' => $user->id,
        ]);
    }
    public function test_user_can_update_workspace(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $updateData = [
            'name' => 'Updated Workspace',
            'description' => 'Updated description',
        ];

        $response = $this->patchJson("/api/workspaces/{$workspace->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Workspace',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
            'name' => 'Updated Workspace',
        ]);
    }
    public function test_user_can_delete_workspace(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/workspaces/{$workspace->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('workspaces', [
            'id' => $workspace->id,
        ]);
    }
}
