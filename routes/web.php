<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/toggle-persona', [ProfileController::class, 'togglePersona'])->name('profile.toggle-persona');

    // Common E-Commerce Routes
    Route::get('/products', [\App\Http\Controllers\Web\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [\App\Http\Controllers\Web\ProductController::class, 'show'])->name('products.show');

    Route::get('/cart', [\App\Http\Controllers\Web\CartController::class, 'index'])->name('cart.index');
    Route::get('/checkout', [\App\Http\Controllers\Web\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [\App\Http\Controllers\Web\CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [\App\Http\Controllers\Web\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Web\OrderController::class, 'show'])->name('orders.show');

    // Admin Only Routes
    Route::middleware('admin')->group(function () {
        Route::delete('/products/{product}', [\App\Http\Controllers\Web\ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/refill', [\App\Http\Controllers\Web\ProductController::class, 'refill'])->name('products.refill');
    });
});

require __DIR__ . '/auth.php';
