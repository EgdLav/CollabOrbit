<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Http\Responses\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Workspace;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
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
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        $data = $validator->validated();
        $user = auth()->user();
        $data['owner_id'] = $user->id;
        $workspace = Workspace::create($data);
        $workspace->users()->attach($user);

        return ApiResponse::success('Workspace successfully created', 201, [
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace)
    {
        //
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
    public function update(Request $request, Workspace $workspace)
    {
        $validator = validator($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        $data = $validator->validated();
        $user = auth()->user();
        if ($user->id != $workspace->owner_id) {
            return ApiResponse::error('Access denied', 403);
        }

        $filteredData = array_filter($data, fn($v) => $v != null);

        $workspace->update($filteredData);

        return ApiResponse::success('Workspace successfully updated', 200, [
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace)
    {
        $user = auth()->user();
        if ($user->id != $workspace->owner_id) {
            return ApiResponse::error('Access denied', 403);
        }
        $workspace->delete();
        return response()->noContent();
    }
}
