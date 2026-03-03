<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request) {
        $validator = validator($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error('Validation error!', 422, $validator->errors());
        }
        $data = $validator->validated();
        $user = User::create($data);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
    public function login(Request $request) {
        $validator = validator($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error('Validation error!', 422, $validator->errors());
        }
        $data = $validator->validated();
        $user = User::create($data);
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
