<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShippingLabelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Shipping Labels routes
    Route::prefix('shipping-labels')->group(function () {
        Route::get('/', [ShippingLabelController::class, 'index']);
        Route::post('/', [ShippingLabelController::class, 'store']);
        Route::get('/{id}', [ShippingLabelController::class, 'show']);
        Route::delete('/{id}', [ShippingLabelController::class, 'destroy']);
        Route::post('/rates', [ShippingLabelController::class, 'getRates']);
    });
});
