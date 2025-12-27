<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Enums\StockStatus;
use App\Enums\SystemCommand;

class DashboardController extends Controller
{
    public function index()
    {
        // General Stats
        $totalProducts = Product::count();
        $totalValue = Product::sum(DB::raw('price * stock_quantity'));
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $outOfStockCount = Product::where('stock_quantity', 0)->count();

        // Chart Data: Products per Category
        $categoryData = Product::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        // Chart Data: Stock distribution
        $stockStatusData = [
            'in_stock' => Product::where('stock_quantity', '>', 10)->count(),
            'low_stock' => Product::whereBetween('stock_quantity', [1, 10])->count(),
            'out_of_stock' => $outOfStockCount,
        ];

        // Products for command dropdowns
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_products' => $totalProducts,
                'total_inventory_value' => (float) $totalValue,
                'low_stock' => $lowStockCount,
                'out_of_stock' => $outOfStockCount,
            ],
            'charts' => [
                'categories' => [
                    'labels' => $categoryData->pluck('category')->map(fn($c) => ucfirst($c))->toArray(),
                    'data' => $categoryData->pluck('count')->toArray(),
                ],
                'stock_status' => [
                    'labels' => [
                        StockStatus::IN_STOCK->label(),
                        StockStatus::LOW_STOCK->label(),
                        StockStatus::OUT_OF_STOCK->label(),
                    ],
                    'data' => array_values($stockStatusData),
                ],
            ],
            'products' => $products,
        ]);
    }

    public function runCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string|in:' . implode(',', SystemCommand::values()),
            'params' => 'nullable|array',
            'email' => 'required|email', // All our existing commands require email mapping
        ]);

        $command = $request->command;
        $params = $request->params ?? [];
        $email = $request->email;

        $commandEnum = SystemCommand::tryFrom($command);

        // Inject email into params for specific commands
        if ($commandEnum && $commandEnum->requiresEmail()) {
            $params['email'] = $email;
        }

        try {
            Artisan::call($command, $params);
            $output = Artisan::output();

            return back()->with('success', "Action dispatched to background queue.\nLog: " . trim($output));
        } catch (\Exception $e) {
            return back()->with('error', 'Command failure: ' . $e->getMessage());
        }
    }
}
