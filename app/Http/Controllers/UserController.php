<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        $query = User::query();
        if ($dep = $request->department) {
            $query->where('users.department', $dep);
        }
        if ($name = $request->name) {
            $query->where(function ($q) use ($name) {
                $q->where('users.first_name', 'like', "%$name%")
                    ->orWhere('users.last_name', 'like', "%$name%");
            });
        }
        $users = $query->paginate(10);

        return ApiResponse::success(data:[
            'users' => UserResource::collection($users->items()),
            'pagination' => [
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }
    public function show(Request $request, User $user) {
        return ApiResponse::success(data:[
            'user' => new UserResource($user),
        ]);
    }
    public function me(Request $request) {
        return ApiResponse::success(data:[
            'user' => new UserResource($request->user()),
        ]);
    }
}
