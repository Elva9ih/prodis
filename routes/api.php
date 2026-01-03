<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Server discovery endpoint (for local network detection)
Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'server_id' => config('app.server_id', 'prodis-server'),
        'timestamp' => now()->timestamp,
    ]);
});

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes - require authentication
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Questions - available to all authenticated users
    Route::get('/questions', [QuestionController::class, 'index']);
});

// Agent-only routes with local network restriction
Route::middleware(['auth:sanctum', 'agent', 'local.network'])->group(function () {
    // Sync endpoint
    Route::post('/sync/establishments', [SyncController::class, 'syncEstablishments']);
});
