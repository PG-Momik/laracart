<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ProductResource;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display the product listing page.
     */
    public function index(Request $request): Response|\Illuminate\Http\JsonResponse|AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'category', 'tags', 'min_price', 'max_price', 'in_stock', 'sort_by', 'sort_order', 'layout', 'page', 'per_page']);

        $query = Product::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // Category
        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->where('category', $request->input('category'));
        }

        // Tags
        if ($request->filled('tags')) {
            $tags = is_array($request->input('tags')) ? $request->input('tags') : explode(',', $request->input('tags'));
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        // Price Range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // In Stock
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 12);
        $products = $query->paginate($perPage)->withQueryString();

        if ($request->has('json') || $request->wantsJson() || ($request->ajax() && !$request->header('X-Inertia'))) {
            return ProductResource::collection($products);
        }

        // Fetch unique categories
        $categories = Product::distinct()->pluck('category')->filter()->values();

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the product details page.
     */
    public function show(int $id): Response
    {
        $product = Product::findOrFail($id);

        return Inertia::render('Products/Show', [
            'product' => new ProductResource($product),
        ]);
    }

    public function refill(int $id): \Illuminate\Http\RedirectResponse
    {
        $product = Product::findOrFail($id);
        $added = $product->total_stock - $product->stock_quantity;

        if ($added > 0) {
            $product->update(['stock_quantity' => $product->total_stock]);

            // Email user about restock (queued automatically)
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)
                ->queue(new \App\Mail\ProductRestockMail($product, (int) $added));
        }

        return redirect()->back()->with('success', "Product {$product->name} restocked to full capacity (+{$added} units)");
    }

    /**
     * Remove the product from storage (Demo logic).
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')->with('success', "Product {$name} deleted successfully.");
    }
}
