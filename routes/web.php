<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Illuminate\Support\Facades\Auth::check()) {
        return Auth::user()->is_admin
            ? redirect()->route('dashboard')
            : redirect()->route('products.index');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/toggle-persona', [ProfileController::class, 'togglePersona'])->name('profile.toggle-persona');

    // Admin Only Sections
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Rate limit admin commands to prevent abuse
        // Route::middleware('throttle:10,1')->group(function () {
        Route::post('/dashboard/command', [DashboardController::class, 'runCommand'])->name(
            'dashboard.command'
        );
        // });

        // Rate limit destructive operations
        // Route::middleware('throttle:30,1')->group(function () {
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name(
            'products.destroy'
        );
        Route::post('/products/{product}/refill', [ProductController::class, 'refill'])->name(
            'products.refill'
        );
        // });
    });

    // Customer Only Sections
    Route::middleware('customer')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name(
            'products.show'
        );
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

        // Rate limit checkout to prevent spam orders
        // Route::middleware('throttle:10,1')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name(
            'checkout.store'
        );
        // });

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    });
});
