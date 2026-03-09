<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\FurnitureController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (nécessitent une authentification via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/recommendations', [AuthController::class, 'recommendations']);

    // Rooms
    Route::apiResource('rooms', RoomController::class);
    Route::get('/rooms/{room}/surface', [RoomController::class, 'surface']);
    Route::post('/rooms/{room}/photos', [RoomController::class, 'addPhoto']);

    // Photos
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
    Route::post('/photos/{photo}/analyze', [PhotoController::class, 'analyze']);

    // Designs
    Route::apiResource('designs', DesignController::class);
    Route::post('/rooms/{room}/designs', [DesignController::class, 'store']); // Créer un design pour une pièce
    Route::post('/designs/{design}/generate', [DesignController::class, 'generate']);

    // Furniture
    Route::apiResource('furniture', FurnitureController::class)->only(['index', 'show']);
    Route::get('/furniture/{furniture}/compare', [FurnitureController::class, 'compare']);
});