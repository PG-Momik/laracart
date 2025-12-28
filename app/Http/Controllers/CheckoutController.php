<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Store a newly created resource in storage (Place Order).
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            abort(400, 'Cart is empty');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return DB::transaction(function () use ($user, $cartItems, $total) {
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => OrderStatus::PENDING,
            ]);

            $orderItems = [];
            $now = now();

            foreach ($cartItems as $item) {
                $orderItems[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $item->product->price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            OrderItem::insert($orderItems);

            // Clear cart (Stock was already handled in CartController)
            CartItem::where('user_id', $user->id)->delete();

            return response()->json([
                'data' => $order->load('items'),
            ]);
        });
    }
}
