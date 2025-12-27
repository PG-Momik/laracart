<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Mail\StockAlertMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * For Testing only: Send email instantly to alert admin that stock is runnin out
 */
class SendStockAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-stock-alert {product_id} {email} {--status=LOW STOCK}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instantly send a stock alert mail for a product';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('product_id');
        $email = $this->argument('email');
        $status = $this->option('status');

        $product = Product::find($productId);

        if (!$product) {
            $this->error("Product with ID {$productId} not found.");
            return Command::FAILURE;
        }

        $this->info("Queuing {$status} alert for {$product->name} to {$email}...");

        Mail::to($email)->queue(new StockAlertMail($product, $status));

        $this->info('Email queued successfully.');

        return Command::SUCCESS;
    }
}
