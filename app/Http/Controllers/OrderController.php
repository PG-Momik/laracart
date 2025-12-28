<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): OrderResource
    {
        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return new OrderResource($order);
    }
}
