<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use function Laravel\Prompts\error;

class AuthController extends Controller
{
    //
    public function register(Request $request) {
        $validator = validator($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|max:10240',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        $data = $validator->validated();
        $avatar = $request->avatar ?? null;
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $user = User::create($data)->refresh();

        Mail::to($user->email)->send(new VerifyEmail($user));

        return ApiResponse::success('Check your email to verify your account.', 201, [
            'user' => new UserResource($user),
        ]);
    }
    public function login(Request $request) {
        $validator = validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error(errors: $validator->errors());
        }
        if (!auth()->attempt($request->only('email', 'password'))) {
            return ApiResponse::error('Authentication failed', 401);
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;


        return ApiResponse::success('Successfully logged in', 200, [
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
    public function verifyEmail(Request $request, User $user, $hash)
    {
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))){
            return ApiResponse::error('Access denied', 403);
        }
        $user->markEmailAsVerified();
        return ApiResponse::success('Email verified successfully');
    }
}
