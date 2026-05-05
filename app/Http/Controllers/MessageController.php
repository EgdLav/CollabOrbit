<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoreRequest;
use App\Http\Resources\MessageResource;
use App\Http\Responses\ApiResponse;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chat $chat)
    {
        $this->authorize('view', $chat);
        $messages = $chat->messages()
            ->with('user')
            ->latest()
            ->paginate(20);

        return ApiResponse::success(data: [
            'messages' => MessageResource::collection($messages),
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
    public function store(MessageStoreRequest $request, Chat $chat)
    {
        $message = $chat->messages()->create([
            'user_id' => auth()->id(),
            'body' => trim($request->body),
        ]);

        $message->load('user');

        return ApiResponse::success(code: 201, data: [
            'message' => new MessageResource($message),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }

}
