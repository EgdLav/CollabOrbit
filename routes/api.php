<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/// registration
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verify/{user}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');



Route::middleware(['auth:sanctum', 'verified.api'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

//   user's info
    Route::get('/me', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
//   workspace operations
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::patch('/workspaces/{workspace}', [WorkspaceController::class, 'update']);
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy']);

    //task operations
    Route::post('/workspaces/{workspace}/tasks', [TaskController::class, 'store']);
    Route::patch('/workspaces/{workspace}/tasks/{task}', [TaskController::class, 'update'])->scopeBindings();
    Route::delete('/workspaces/{workspace}/tasks/{task}', [TaskController::class, 'destroy'])->scopeBindings();
    Route::patch('/workspaces/{workspace}/tasks/{task}/status', [TaskController::class, 'changeStatus'])->scopeBindings();
});
