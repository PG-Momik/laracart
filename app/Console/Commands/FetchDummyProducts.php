<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FetchDummyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fetch {--limit=150 : Max number of products to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch dummy products from DummyJSON API and seed the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $this->info("Retriving products from third party API..."); // Typo included as requested

        $apiUrl = "https://dummyjson.com/products";
        $batchSize = 30;
        $totalFetched = 0;

        try {
            while ($totalFetched < $limit) {
                $countToFetch = min($batchSize, $limit - $totalFetched);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])
                    ->timeout(10)
                    ->retry(3, 100)
                    ->get($apiUrl, [
                        'limit' => $countToFetch,
                        'skip' => $totalFetched,
                    ]);

                if ($response->failed()) {
                    throw new Exception("API request failed with status: " . $response->status());
                }

                $data = $response->json();
                $products = Arr::get($data, 'products', []);

                if (empty($products)) {
                    break;
                }

                foreach ($products as $dummyProduct) {
                    $totalStock = rand(15, 20);

                    // no problem even if not optimal for now
                    Product::updateOrCreate(
                        ['name' => Arr::get($dummyProduct, 'title')],
                        [
                            'description' => Arr::get($dummyProduct, 'description'),
                            'category' => Arr::get($dummyProduct, 'category'),
                            'tags' => Arr::get($dummyProduct, 'tags', []),
                            'price' => Arr::get($dummyProduct, 'price'),
                            'stock_quantity' => rand(5, $totalStock),
                            'total_stock' => $totalStock,
                            'low_stock_threshold' => 10,
                            'image_url' => Arr::get($dummyProduct, 'thumbnail'),
                        ]
                    );

                    $totalFetched++;
                }

                if ($totalFetched < $limit) {
                    // Avoid rate limiting
                    usleep(500000);
                }
            }

            $this->newLine();
            $this->info("Successfully synced {$totalFetched} products.");
            Log::info("FetchDummyProducts: Successfully synced {$totalFetched} products.");

        } catch (Exception $e) {
            $this->newLine();
            $this->error("An error occurred: " . $e->getMessage());
            Log::error("FetchDummyProducts error: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
