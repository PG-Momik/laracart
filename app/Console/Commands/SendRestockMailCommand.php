<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use App\Mail\ProductRestockMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * For tEsting only: Send email instantly saying yo product been restocked.
 */
class SendRestockMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-restock-mail {product_id} {quantity=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instantly send a restock confirmation mail for a product';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('product_id');
        $quantity = (int) $this->argument('quantity');

        $product = Product::find($productId);

        if (!$product) {
            $this->error("Product with ID {$productId} not found.");
            return Command::FAILURE;
        }

        $this->info("Sending restock mail for {$product->name} (+{$quantity} units) to momik.shrestha@gmail.com...");

        Mail::to('momik.shrestha@gmail.com')->send(new ProductRestockMail($product, $quantity));

        $this->info('Email sent successfully.');

        return Command::SUCCESS;
    }
}
