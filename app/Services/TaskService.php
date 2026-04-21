<?php

namespace App\Services;

use App\Events\TaskStatusChanged;
use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    public function create(Request $request, Workspace $workspace, array $data): ?Task {
        if (!$workspace->users()->where('users.id', $data['executor_id'])->exists()) {
            return null;
        }

        $paths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store('task_files', 'public');
            }
        }
        if ($request->hasFile('preview')) {
            $data['preview'] = $request->file('preview')->store('previews', 'public');
        }

        $data['files'] = $paths;
        $data['workspace_id'] = $workspace->id;
        $data['creator_id'] = $request->user()->id;
        $task = Task::create($data)->fresh();
        return $task;
    }

    public function update(Request $request, Task $task, array $data): ?Task {
        $paths = [];

        if ($request->hasFile('files')) {
            foreach ($task->files as $file) {
                Storage::disk('public')->delete($file);
            }
            foreach ($request->file('files') as $file) {
                $paths[] = $file->store('task_files', 'public');
            }
            $data['files'] = $paths;
        }
        if ($request->hasFile('preview')) {
            Storage::disk('public')->delete($task->preview);
            $data['preview'] = $request->file('preview')->store('previews', 'public');
        }
        $filteredData = array_filter($data, fn($v) => $v !== null);
        $task->update($filteredData);
        return $task->fresh();
    }
    public function changeCategory(Task $task, Workspace $workspace, array $data): ?Task {
        $task->update($data);
        return $task->fresh();
    }
}
