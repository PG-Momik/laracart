<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * Display the order history page.
     */
    public function index(): Response
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => OrderResource::collection($orders),
        ]);
    }

    /**
     * Display the order details page.
     */
    public function show(int $id): Response
    {
        $order = Order::with('items.product')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Orders/Show', [
            'order' => ['data' => (new OrderResource($order))->resolve()],
        ]);
    }
}
