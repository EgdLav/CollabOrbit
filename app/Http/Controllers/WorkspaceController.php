<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceStoreRequest;
use App\Http\Requests\WorkspaceUpdateRequest;
use App\Http\Resources\WorkspaceResource;
use App\Http\Responses\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{

    public function __construct(private WorkspaceService $workspaceService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $workspaces = auth()->user()->workspaces();
        if ($n = $request->search) {
            $workspaces = $workspaces->where('name', 'like', "%$n%")->orWhere('description', 'like', "%$n%")->orWhere('slug', 'like', "%$n%");
        }
        $workspaces = $workspaces->get();
        return ApiResponse::success(data: WorkspaceResource::collection($workspaces));
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
    public function store(WorkspaceStoreRequest $request)
    {
        $workspace = $this->workspaceService->create(
            $request->validated(),
            auth()->user(),
        );
        return ApiResponse::success('Workspace successfully created', 201, [
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        return ApiResponse::success(data: new WorkspaceResource($workspace));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workspace $workspace)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkspaceUpdateRequest $request, Workspace $workspace)
    {
        $workspace = $this->workspaceService->update(
            $request->validated(),
            $workspace,
            auth()->user(),
        );

        return ApiResponse::success('Workspace successfully updated', 200, [
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace)
    {
        if (Gate::denies('delete', $workspace)) {
            return ApiResponse::error('Access denied', 403);
        }
        $this->workspaceService->delete($workspace);
        return response()->noContent();
    }
}
