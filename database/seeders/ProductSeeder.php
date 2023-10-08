<?php

namespace Database\Seeders;

use App\Constants\ProductTypes;
use App\Models\CombinedProductChild;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $burgerProduct = Product::firstOrCreate(
            ['name' => 'Burger'], [
                'name' => 'Burger',
                'description' => 'Great sandwich',
                'product_type' => ProductTypes::COMBINED(),
                'sku' => Str::random(rand(8, 12)),
            ]);

        $products = [
            [
                'name' => 'Beef',
                'description' => 'Great Beef',
                'product_type' => ProductTypes::WEIGHT(),
                'sku' => Str::random(rand(8, 12)),
                'current_stock' => '20000',
                'quantity' => '150'
            ],
            [
                'name' => 'Cheese',
                'description' => 'Great Cheese',
                'product_type' => ProductTypes::WEIGHT(),
                'sku' => Str::random(rand(8, 12)),
                'current_stock' => '5000',
                'quantity' => '30'
            ],
            [
                'name' => 'Onion',
                'description' => 'Great Onion',
                'product_type' => ProductTypes::WEIGHT(),
                'sku' => Str::random(rand(8, 12)),
                'current_stock' => '1000',
                'quantity' => '20'
            ]
        ];

        collect($products)->each(function ($product) use ($burgerProduct) {
            $childProduct = Product::firstOrCreate(['name' => $product['name']], collect($product)->except('quantity')->toArray());

            CombinedProductChild::firstOrCreate([
                    'child_product_id' => $childProduct->id,
                    'product_id' => $burgerProduct->id,
                    'quantity' => $product['quantity']
                ]);
        });
    }
}
