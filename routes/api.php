<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart Routes
    Route::apiResource('cart', CartController::class)->names('api.cart')->only(['index', 'store', 'update', 'destroy']);

    // Order Routes
    Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('api.checkout.store');
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
});
