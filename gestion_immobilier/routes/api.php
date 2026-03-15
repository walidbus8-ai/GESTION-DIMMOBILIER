<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\FurnitureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- 1. Routes Publiques (ما محتاجاش Token ومفتوحة للتجربة) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/**
 * هاد السطر خرجناه برا الـ middleware باش نتفاداو الـ 401
 * وزدنا علامة "?" باش الـ ID يولي اختياري وما يعطيكش 404 إيلا كان الـ design ماكاينش في الداتابيز
 */
Route::post('/designs/{design?}/generate', [DesignController::class, 'generate']);


// --- 2. Routes Protégées (ضروري يكون المستخدم مسجل) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/recommendations', [AuthController::class, 'recommendations']);

    // Rooms (المربعات/الغرف)
    Route::apiResource('rooms', RoomController::class);
    Route::get('/rooms/{room}/surface', [RoomController::class, 'surface']);
    Route::post('/rooms/{room}/photos', [RoomController::class, 'addPhoto']);

    // Photos
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
    Route::post('/photos/{photo}/analyze', [PhotoController::class, 'analyze']);

    // Designs (CRUD العادي)
    Route::apiResource('designs', DesignController::class)->except(['store']);
    Route::post('/rooms/{room}/designs', [DesignController::class, 'store']); 

    // Furniture (الأثاث)
    Route::apiResource('furniture', FurnitureController::class)->only(['index', 'show']);
    Route::get('/furniture/{furniture}/compare', [FurnitureController::class, 'compare']);
});