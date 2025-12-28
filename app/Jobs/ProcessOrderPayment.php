<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\QueueName;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order
    ) {
        $this->onQueue(QueueName::HIGH->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing payment for Order #{$this->order->id}");

        // Simulate payment processing delay
        sleep(2);

        // Update order status
        $this->order->markAsCompleted();

        Log::info("Order #{$this->order->id} payment processed successfully.");
    }
}
