<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\ProductRestockMail;
use App\Models\Product;
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
    protected $signature = 'app:send-restock-mail {product_id} {email} {quantity=10}';

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
        $email     = $this->argument('email');
        $quantity  = (int)$this->argument('quantity');

        $product = Product::find($productId);

        if (!$product) {
            $this->error("Product with ID {$productId} not found.");
            return Command::FAILURE;
        }

        $this->info("Queuing restock mail for {$product->name} (+{$quantity} units) to {$email}...");

        Mail::to($email)->queue(new ProductRestockMail($product, $quantity));

        $this->info('Email queued successfully.');

        return Command::SUCCESS;
    }
}
