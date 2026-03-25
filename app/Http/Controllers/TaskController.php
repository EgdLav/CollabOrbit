<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Http\Resources\WorkspaceResource;
use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Workspace $workspace)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'preview' => 'nullable|image|max:10240',
            'files' => 'nullable|array|max:1000',
            'files.*' => 'file|max:10240',
            'executor_id' => 'required|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        $user = auth()->user();
        if ($workspace->owner_id != $user->id) {
            return ApiResponse::error('Access denied', 403);
        }
        $data = $validator->validated();
        $executor = User::find($data['executor_id']);
        if (!$workspace->users()->get()->contains($executor)) {
            return ApiResponse::error('Executor must be in workspace', 403);
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
        $task = Task::create($data)->refresh();

        return ApiResponse::success('Task successfully created', 201, [
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workspace $workspace, Task $task)
    {
        $validator = validator($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'preview' => 'nullable|image|max:10240',
            'files' => 'nullable|array|max:1000',
            'files.*' => 'file|max:10240',
            'executor_id' => 'nullable|integer|exists:users,id',
        ]);
        if ($workspace->owner_id !== auth()->id()) {
            return ApiResponse::error('Access denied', 403);
        }
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        $data = $validator->validated();
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
        $filteredData = array_filter($data, fn($v) => $v != null);
        $task->update($filteredData);

        return ApiResponse::success('Task successfully updated', 200, [
            'task' => new TaskResource($task),
        ]);
    }
    public function changeStatus(Request $request, Workspace $workspace, Task $task)
    {
        $validator = validator($request->all(), [
            'status' => 'required|string|in:new,in progress,on edge,completed',
        ]);
        if (auth()->id() != $workspace->owner_id || auth()->id() != $task->executor_id) {
            return ApiResponse::error('Access denied', 403);
        }
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        $data = $validator->validated();
        if ($data['status'] == 'completed' && auth()->id() != $workspace->owner_id) {
            return ApiResponse::error('Access denied', 403);
        }
        $task->update($data);

        return ApiResponse::success('Task successfully updated', 200, [
            'task' => new TaskResource($task),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {

    }
}
