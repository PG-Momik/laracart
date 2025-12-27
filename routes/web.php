<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
        Route::get('/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/command', [\App\Http\Controllers\Web\DashboardController::class, 'runCommand'])->name('dashboard.command');

        Route::delete('/products/{product}', [\App\Http\Controllers\Web\ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/refill', [\App\Http\Controllers\Web\ProductController::class, 'refill'])->name('products.refill');
    });

    // Customer Only Sections
    Route::middleware('customer')->group(function () {
        Route::get('/products', [\App\Http\Controllers\Web\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [\App\Http\Controllers\Web\ProductController::class, 'show'])->name('products.show');

        Route::get('/cart', [\App\Http\Controllers\Web\CartController::class, 'index'])->name('cart.index');
        Route::get('/checkout', [\App\Http\Controllers\Web\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [\App\Http\Controllers\Web\CheckoutController::class, 'store'])->name('checkout.store');

        Route::get('/orders', [\App\Http\Controllers\Web\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [\App\Http\Controllers\Web\OrderController::class, 'show'])->name('orders.show');
    });
});

require __DIR__ . '/auth.php';
