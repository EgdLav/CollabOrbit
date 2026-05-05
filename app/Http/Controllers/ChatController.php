<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Http\Responses\ApiResponse;
use App\Models\Chat;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chats = $request->user()->chats()->latest()->get();
        $chats->load([
            'users',
            'lastMessage',
        ]);

        return ApiResponse::success(data: [
            'chats' => ChatResource::collection($chats),
        ]);
    }
    public function byWorkspace(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        $chat = $workspace->chat;
        $chat->load([
            'users',
            'lastMessage',
        ]);

        return ApiResponse::success(data: [
            'chat' => new ChatResource($chat),
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
    public function store(Request $request, User $user)
    {
        $authId = auth()->id();

        $chat = Chat::where('type', 'private')
            ->whereHas('users', fn($q) => $q->where('users.id', $authId))
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->first();

        if ($chat) {
            return ApiResponse::success(data: [
                'chat' => new ChatResource($chat),
            ]);
        }

        // create new
        $chat = Chat::create([
            'type' => 'private',
        ]);

        $chat->users()->attach([$authId, $user->id]);
        $chat->load(['lastMessage']);

        return ApiResponse::success(data: [
            'chat' => new ChatResource($chat),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        $this->authorize('view', $chat);
        $chat->load([
            'users',
            'lastMessage',
        ]);

        return ApiResponse::success(data: [
            'chat' => new ChatResource($chat),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
