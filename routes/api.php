<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart Routes - Higher limit for frequent operations
    Route::middleware('throttle:120,1')->group(function () {
        Route::apiResource('cart', CartController::class)
            ->names('api.cart')
            ->only(['index', 'store', 'update', 'destroy']);
    });

    // Checkout - Lower limit for sensitive operations
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])
            ->name('api.checkout.store');
    });

    // Order Routes - Moderate limit for viewing operations
    Route::middleware('throttle:60,1')->group(function () {
        Route::apiResource('orders', OrderController::class)
            ->names('api.orders')
            ->only(['index', 'show']);
    });
});
