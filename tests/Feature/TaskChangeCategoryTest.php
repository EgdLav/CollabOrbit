<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskChangeCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_executor_can_change_category(): void
    {
        $creator = User::factory()->create();
        $executor = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $creator->id,
        ]);

        $workspace->users()->attach([$creator->id, $executor->id]);

        $category1 = Category::create([
            'workspace_id' => $workspace->id,
            'name' => 'Old',
        ]);

        $category2 = Category::create([
            'workspace_id' => $workspace->id,
            'name' => 'New',
        ]);

        $task = $this->insertTask([
            'workspace_id' => $workspace->id,
            'category_id' => $category1->id,
            'creator_id' => $creator->id,
            'executor_id' => $executor->id,
        ]);

        $this->actingAs($executor, 'sanctum');

        $response = $this->patchJson(
            "/api/workspaces/{$workspace->id}/categories/{$category1->id}/tasks/{$task->id}/category",
            ['category_id' => $category2->id]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category_id' => $category2->id,
        ]);
    }

    public function test_random_user_cannot_change_category(): void
    {
        $owner = User::factory()->create();
        $random = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $workspace->users()->attach($owner->id);

        $category = Category::create([
            'workspace_id' => $workspace->id,
            'name' => 'Main',
        ]);

        $task = $this->insertTask([
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'creator_id' => $owner->id,
            'executor_id' => $owner->id,
        ]);

        $this->actingAs($random, 'sanctum');

        $response = $this->patchJson(
            "/api/workspaces/{$workspace->id}/categories/{$category->id}/tasks/{$task->id}/category",
            ['category_id' => $category->id]
        );

        $response->assertStatus(403);
    }

    public function test_validation_fails_if_category_not_exists_in_workspace(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
        ]);

        $workspace->users()->attach($user->id);

        $category = Category::create([
            'workspace_id' => $workspace->id,
            'name' => 'Valid',
        ]);

        $task = $this->insertTask([
            'workspace_id' => $workspace->id,
            'category_id' => $category->id,
            'creator_id' => $user->id,
            'executor_id' => $user->id,
        ]);

        $otherWorkspace = Workspace::factory()->create();

        $otherCategory = Category::create([
            'workspace_id' => $otherWorkspace->id,
            'name' => 'Other',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->patchJson(
            "/api/workspaces/{$workspace->id}/categories/{$category->id}/tasks/{$task->id}/category",
            ['category_id' => $otherCategory->id]
        );

        $response->assertStatus(422);
    }

    private function insertTask(array $data): Task
    {
        DB::table('tasks')->insert([
            'name' => 'Task for category change',
            'description' => 'Task description',
            'preview' => 'previews/default.png',
            'files' => json_encode([]),
            'workspace_id' => $data['workspace_id'],
            'category_id' => $data['category_id'],
            'creator_id' => $data['creator_id'],
            'executor_id' => $data['executor_id'],
            'due_date' => now()->addDay()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Task::query()->latest('id')->firstOrFail();
    }
}
