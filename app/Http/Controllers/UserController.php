<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        $user = auth()->user();
        return ApiResponse::success(data:[
            'user' => new UserResource($user),
        ]);
    }
    public function show(Request $request, User $user) {
        return ApiResponse::success(data:[
            'user' => new UserResource($user),
        ]);
    }
}
