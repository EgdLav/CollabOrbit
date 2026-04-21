<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_category(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'owner-workspace',
        ]);
        $workspace->users()->attach($user->id);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/workspaces/{$workspace->id}/categories", [
            'name' => 'Backend',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'name' => 'Backend',
            'workspace_id' => $workspace->id,
        ]);
    }

    public function test_non_owner_cannot_create_category(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'team-workspace',
        ]);
        $workspace->users()->attach([$owner->id, $user->id]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/workspaces/{$workspace->id}/categories", [
            'name' => 'Backend',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_update_category(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'category-workspace',
        ]);
        $workspace->users()->attach($user->id);

        $category = Category::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'Initial',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->patchJson("/api/workspaces/{$workspace->id}/categories/{$category->id}", [
            'name' => 'Updated',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated',
        ]);
    }

    public function test_owner_can_delete_category(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'slug' => 'delete-category-workspace',
        ]);
        $workspace->users()->attach($user->id);

        $category = Category::query()->create([
            'workspace_id' => $workspace->id,
            'name' => 'To delete',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/workspaces/{$workspace->id}/categories/{$category->id}");

        $response->assertStatus(200)->assertJsonPath('success', true);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
