<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function index(): Response
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'cartTotal' => $total,
        ]);
    }
}
