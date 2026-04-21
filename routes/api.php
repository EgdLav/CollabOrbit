<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
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
Route::get('/email/verify', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', ])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

//   user's info
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
//   workspace operations
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::patch('/workspaces/{workspace}', [WorkspaceController::class, 'update']);
    Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show']);
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy']);
//   workspace's categories operations
    Route::post('/workspaces/{workspace}/categories', [CategoryController::class, 'store']);
    Route::patch('/workspaces/{workspace}/categories/{category}', [CategoryController::class, 'update'])->scopeBindings();
    Route::delete('/workspaces/{workspace}/categories/{category}', [CategoryController::class, 'destroy'])->scopeBindings();

    //task operations
    Route::post('/workspaces/{workspace}/tasks', [TaskController::class, 'store']);
    Route::patch('/workspaces/{workspace}/tasks/{task}', [TaskController::class, 'update'])->scopeBindings();
    Route::delete('/workspaces/{workspace}/tasks/{task}', [TaskController::class, 'destroy'])->scopeBindings();
    Route::patch('/workspaces/{workspace}/tasks/{task}/category', [TaskController::class, 'changeCategory'])->scopeBindings();
});
