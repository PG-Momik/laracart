<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\StockAlertType;
use App\Mail\StockAlertMail;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Only trigger if stock_quantity has changed
        if ($product->isDirty('stock_quantity')) {
            $newStock = (int)$product->stock_quantity;
            $oldStock = (int)$product->getOriginal('stock_quantity');

            // Trigger alert if stock drops to 5 or less, and it wasn't already at this state or lower
            // This prevents duplicate spam emails if stock goes from 2 to 1.
            if ($newStock <= 5 && ($oldStock > 5 || $newStock == 0 && $oldStock > 0)) {
                $status = $newStock <= 0
                    ? StockAlertType::OUT_OF_STOCK->value
                    : StockAlertType::LOW_STOCK->value;

                Mail::to('test.user@laracart.com')
                    ->queue(new StockAlertMail($product, $status));
            }
        }
    }
}
