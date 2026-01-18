<?php

use App\Http\Controllers\Api\AvailableCarsController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/available-cars', AvailableCarsController::class);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Для тестирования
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'position' => $request->user()->position,
            'available_categories' => $request->user()->getAvailableCategoryIds(),
        ]);
    });
});
