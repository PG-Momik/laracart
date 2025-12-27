<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->word(),
            'tags' => ['tag1', 'tag2'],
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'total_stock' => $this->faker->numberBetween(100, 200),
            'low_stock_threshold' => 10,
            'image_url' => 'https://placehold.co/600x400?text=Product',
        ];
    }
}
