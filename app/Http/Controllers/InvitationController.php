<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationStoreRequest;
use App\Http\Requests\InvitationUpdateRequest;
use App\Http\Resources\InvitationResource;
use App\Http\Responses\ApiResponse;
use App\Models\Invitation;
use App\Models\Workspace;
use App\Services\InvitationService;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __construct(private InvitationService $invitationService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invites = $request->user()->invitations;
        return ApiResponse::success(data:[
            'invites' => InvitationResource::collection($invites),
        ]);
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
    public function store(InvitationStoreRequest $request, Workspace $workspace)
    {
        $invitee_id = $request->input('invitee_id');

        if ($workspace->users()->where('users.id', $invitee_id)->exists()) {
            return ApiResponse::error('Already in workspace', 409);
        }

        if ($workspace->invitations()->where('invitee_id', $invitee_id)->exists()) {
            return ApiResponse::error('Already invited', 409);
        }

        $invitation = $this->invitationService->create($workspace, $invitee_id);
        return ApiResponse::success('Invitation successfully created', 201, [
            'invitation' => new InvitationResource($invitation),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invitation $invitation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvitationUpdateRequest $request, Workspace $workspace, Invitation $invitation)
    {
        $status = $request->input('status');

        $this->invitationService->update($request->user(), $workspace, $invitation, $status);
        return ApiResponse::success("Invitation {$status}", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation)
    {
        //
    }
}
