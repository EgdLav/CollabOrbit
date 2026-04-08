<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $executor = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach($executor);

        $this->actingAs($owner, 'sanctum');

        $response = $this->post("/api/workspaces/{$workspace->id}/tasks", [
            'name' => 'My Task',
            'description' => 'Task description',
            'executor_id' => $executor->id,
            'preview' => UploadedFile::fake()->image('preview.png'),
            'files' => [
                UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'task' => [
                        'id',
                        'name',
                        'description',
                        'preview',
                        'files',
                        'status',
                        'workspace_id',
                        'executor_id',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'My Task',
            'workspace_id' => $workspace->id,
            'executor_id' => $executor->id,
        ]);
    }

    public function test_user_can_update_task(): void
    {
        $owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $executor = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach($executor);

        $task = Task::factory()->create([
            'workspace_id' => $workspace->id,
            'executor_id' => $executor->id,
            'preview' => 'previews/original.png',
            'files' => [],
        ]);

        $this->actingAs($owner, 'sanctum');

        $response = $this->patchJson("/api/workspaces/{$workspace->id}/tasks/{$task->id}", [
            'name' => 'Updated Task',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Task',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task',
        ]);
    }

    public function test_owner_can_complete_task(): void
    {
        $owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $executor = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach($executor);

        $task = Task::factory()->create([
            'workspace_id' => $workspace->id,
            'executor_id' => $executor->id,
            'status' => 'new',
        ]);

        $this->actingAs($owner, 'sanctum');

        $response = $this->patchJson("/api/workspaces/{$workspace->id}/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment([
                'status' => 'completed',
            ]);

    }

    public function test_executor_cannot_complete_task(): void
    {
        $owner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $executor = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach($executor);

        $task = Task::factory()->create([
            'workspace_id' => $workspace->id,
            'executor_id' => $executor->id,
            'status' => 'new',
        ]);

        $this->actingAs($executor, 'sanctum');

        $response = $this->patchJson("/api/workspaces/{$workspace->id}/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);
        $response->assertStatus(403);

    }
}
