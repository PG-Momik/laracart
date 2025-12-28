<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'items' => CartItemResource::collection($cartItems),
            'total' => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request): JsonResponse
    {
        $userId    = Auth::id();
        $productId = Arr::get($request->validated(), 'product_id');
        $quantity  = Arr::get($request->validated(), 'quantity');

        $product = Product::findOrFail($productId);

        if ($product->stock_quantity < $quantity) {
            abort(422, 'Insufficient stock');
        }

        $cartItem = DB::transaction(function () use ($userId, $productId, $product, $quantity) {
            // Decrease stock immediately
            $product->stock_quantity -= $quantity;
            $product->save();

            $cartItem = CartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
                $cartItem->refresh();
            } else {
                $cartItem = CartItem::create([
                    'user_id'    => $userId,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                ]);
            }

            return $cartItem;
        });

        return response()->json([
            'item'    => new CartItemResource($cartItem->load('product')),
            'message' => 'Product added to cart'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, int $id): JsonResponse
    {
        $quantity = Arr::get($request->validated(), 'quantity');
        $userId   = Auth::id();

        $cartItem = CartItem::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        DB::transaction(function () use ($cartItem, $quantity) {
            $diff = $quantity - $cartItem->quantity;

            if ($diff > 0) {
                // Increasing quantity, check stock
                if ($cartItem->product->stock_quantity < $diff) {
                    abort(422, 'Insufficient stock');
                }
                $cartItem->product->stock_quantity -= $diff;
            } else {
                // Decreasing quantity, return stock
                $cartItem->product->stock_quantity += abs($diff);
            }

            $cartItem->product->save();
            $cartItem->quantity = $quantity;
            $cartItem->save();
        });

        return response()->json(['message' => 'Cart updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $cartItem = CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        DB::transaction(function () use ($cartItem) {
            // Return stock
            $cartItem->product->stock_quantity += $cartItem->quantity;
            $cartItem->product->save();

            $cartItem->delete();
        });

        return response()->json(['message' => 'Item removed from cart']);
    }
}
