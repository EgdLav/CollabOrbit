<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
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
    public function store(Request $request, Workspace $workspace, $email)
    {
        $email = $request->input('email');

        if ($workspace->users()->where('email', $email)->exists()) {
            return response()->json(['message' => 'Already in workspace'], 409);
        }
        if (Invitation::where('workspace_id', $workspace->id)
            ->where('email', $email)
            ->exists()) {
            return response()->json(['message' => 'Already invited'], 409);
        }

        $invitation = Invitation::create([
            'workspace_id' => $workspace->id,
            'creator_id' => $request->user()->id,
            'email' => $email,
            'token' => Str::uuid(),
            'expires_at' => now()->addDays(7),
        ]);

        $link = "http://localhost:5173/invite/{$invitation->token}";

        return response()->json(['link' => $link]);
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
    public function update(Request $request, Invitation $invitation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation)
    {
        //
    }
}
