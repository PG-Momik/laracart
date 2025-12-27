<?php

use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart Routes
    Route::apiResource('cart', CartController::class)->only(['index', 'store', 'update', 'destroy']);
});
