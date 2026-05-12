<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
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
    public function index(Request $request, Chat $chat)
    {
        $this->authorize('view', $chat);
        $query = $chat->messages()
            ->with('user')
            ->orderByDesc('id');

        if ($beforeId = $request->query('before_id')) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query
            ->limit($request->query('limit', 30))
            ->get();

        return ApiResponse::success(data:[
            'messages' => MessageResource::collection($messages),
            'has_more' => $messages->count() == $request->query('limit', 30),
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
        broadcast(new MessageSent($message))->toOthers();

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
