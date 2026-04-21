<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskChangeCategoryRequest;
use App\Http\Requests\TaskChangeStatusRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\WorkspaceResource;
use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}
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
    public function store(TaskStoreRequest $request, Workspace $workspace)
    {
        $task = $this->taskService->create($request, $workspace, $request->validated());

        if (!$task) {
            return ApiResponse::error('Executor must be in workspace', 403);
        }
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
    public function update(TaskUpdateRequest $request, Workspace $workspace, Task $task)
    {
        $task = $this->taskService->update($request, $task, $request->validated());

        return ApiResponse::success('Task successfully updated', 200, [
            'task' => new TaskResource($task),
        ]);
    }
    public function changeCategory(TaskChangeCategoryRequest $request, Workspace $workspace, Task $task)
    {
        $task = $this->taskService->changeCategory($task, $workspace, $request->validated());
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
