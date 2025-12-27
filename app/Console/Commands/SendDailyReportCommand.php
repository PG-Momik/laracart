<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\DailySalesReport;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

/**
 * Generates and emails the rich daily sales performance report summary. Called by cronjob.
 */
class SendDailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-report {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and email the daily sales report summary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating daily sales report...');

        $yesterday = now()->subDay();

        $stats = [
            'total_revenue' => Order::where('created_at', '>=', $yesterday)->sum('total_amount'),
            'order_count' => Order::where('created_at', '>=', $yesterday)->count(),
            'items_count' => OrderItem::where('created_at', '>=', $yesterday)->sum('quantity'),
            'top_categories' => DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('order_items.created_at', '>=', $yesterday)
                ->select('products.category', DB::raw('sum(order_items.quantity) as total_quantity'))
                ->groupBy('products.category')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->pluck('total_quantity', 'category')
                ->toArray(),
            'best_product' => DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('order_items.created_at', '>=', $yesterday)
                ->select('products.name', DB::raw('sum(order_items.quantity) as total_quantity'))
                ->groupBy('products.name')
                ->orderByDesc('total_quantity')
                ->first()?->name ?? 'None',
        ];

        // Send results to the provided email
        $email = $this->argument('email');

        Mail::to($email)->queue(new DailySalesReport($stats));

        $this->info("Report generated and queued for: {$email}");

        return Command::SUCCESS;
    }
}
