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
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Queue order confirmation email
        try {
            Mail::to($this->order->user)->queue(new OrderPlacedMail($this->order));
            Log::info("Order confirmation email queued for {$this->order->user->email}");
        } catch (\Exception $e) {
            Log::error("Failed to queue order confirmation email: " . $e->getMessage());
        }
    }
}
