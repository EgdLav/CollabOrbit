<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Jobs\SendVerifyEmail;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\FlareClient\Api;
use function Laravel\Prompts\error;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}
    //
    public function register(RegisterRequest $request) {
        $user = $this->authService->register(
            $request->validated(),
            $request->file('avatar')
        );
        return ApiResponse::success('Check your email to verify your account.', 201, [
            'user' => new UserResource($user),
        ]);
    }
    public function login(LoginRequest $request) {
        $token = $this->authService->login($request->only('email', 'password'));
        if (!$token) {
            return ApiResponse::error('Authentication failed', 401);
        }
        if (!auth()->user()->hasVerifiedEmail()) {
            return ApiResponse::error('Email not verified', 403);
        }
        return ApiResponse::success('Successfully logged in', 200, [
            'token' => $token,
        ]);
    }
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
    public function verifyEmail(Request $request)
    {
        if ($request->token === null) {
            return ApiResponse::error('Invalid token', 400);
        }
        $user = User::where('email_token', $request->token)->first();

        if (!$user) {
            return ApiResponse::error('Invalid token', 400);
        }

        $user->email_verified_at = now();
        $user->email_token = null;
        $user->save();

        return response()->json(['message' => 'Email verified']);
//        if (!hash_equals($hash, sha1($user->getEmailForVerification()))){
//            return ApiResponse::error('Access denied', 403);
//        }
//        $user->markEmailAsVerified();
//        return ApiResponse::success('Email verified successfully');
    }
}
