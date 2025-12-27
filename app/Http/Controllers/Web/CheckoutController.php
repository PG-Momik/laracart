<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function index(): Response|RedirectResponse
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return Inertia::render('Checkout/Index', [
            'cart' => [
                'items' => CartItemResource::collection($cartItems)->resolve(),
                'total' => $total,
                'user' => $user,
            ]
        ]);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        try {
            $order = DB::transaction(function () use ($user, $cartItems, $total) {
                // Create Order
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $total,
                    'status' => OrderStatus::PENDING,
                ]);

                // Create Order Items
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_purchase' => $item->product->price,
                    ]);
                }

                // Clear Cart
                CartItem::where('user_id', $user->id)->delete();

                return $order;
            });

            // Dispatch payment processing job to high priority queue
            \App\Jobs\ProcessOrderPayment::dispatch($order);

            return redirect()->route('orders.show', $order->id)->with('success', 'Order placed successfully! Payment is being processed.');
        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to place order.');
        }
    }
}
