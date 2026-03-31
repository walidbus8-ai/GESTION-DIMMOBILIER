<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FurnitureController;

// --- مسارات عامة (بدون تسجيل دخول) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/designs/generate', [DesignController::class, 'generate']);

// انقليه هنا ليعمل بدون الحاجة لـ Token
Route::post('/chat', [ChatController::class, 'chat']);

// --- مسارات محمية (تحتاج تسجيل دخول) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/recommendations', [AuthController::class, 'recommendations']);

    Route::apiResource('rooms', RoomController::class);
    Route::get('/rooms/{room}/surface', [RoomController::class, 'surface']);
    Route::post('/rooms/{room}/photos', [RoomController::class, 'addPhoto']);

    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
    Route::post('/photos/{photo}/analyze', [PhotoController::class, 'analyze']);

    Route::apiResource('designs', DesignController::class)->except(['store']);
    Route::post('/rooms/{room}/designs', [DesignController::class, 'store']); 

    Route::apiResource('furniture', FurnitureController::class)->only(['index', 'show']);
    Route::get('/furniture/{furniture}/compare', [FurnitureController::class, 'compare']);
});

// الـ Headers يفضل أن تكون في Middleware ولكن وضعها هنا في الأسفل كحل مؤقت 

header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization, Accept');