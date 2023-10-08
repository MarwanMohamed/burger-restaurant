<?php

namespace Database\Factories;

use App\Constants\ProductTypes;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CombinedProductChild>
 */
class CombinedProductChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory()->create()->id,
            'child_product_id' => Product::factory()->create([
                'name' => fake()->name(),
                'description' => fake()->text(),
                'product_type' => ProductTypes::WEIGHT(),
                'sku' => fake()->text(8),
                'current_stock' => fake()->randomDigit(),
                'quantity' => fake()->randomDigit()
            ])->id,
            'quantity' => fake()->randomDigit()
        ];
    }
}
