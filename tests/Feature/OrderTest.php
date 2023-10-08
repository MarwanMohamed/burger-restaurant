<?php

namespace Feature;

use App\Constants\ProductTypes;
use App\Mail\ProductStockIsLow;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testValidatesProductExist()
    {
        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => 50,
                    'quantity' => 1
                ]
            ]
        ])->assertJsonValidationErrorFor('product_id');
    }

    public function testValidatesAtLeastOneProductExist()
    {
        $response = $this->postJson(route('api.orders.store'), [
            'products' => [

            ]
        ]);
        $response->assertJsonPath('message', 'The products field is required.');
    }

    public function testValidatesAtLeastOneAndValidQuantityExist()
    {
        $product = Product::factory()->create();
        $ingredient = Product::factory()->create([
            'product_type' => ProductTypes::WEIGHT(),
            'current_stock' => 2000,
        ]);

        $product->children()->sync([$ingredient->id => ['quantity' => 100]]);

        $response = $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => -1
                ]
            ]
        ]);
        $response->assertStatus(422)->assertJsonPath('message', 'The products.0.quantity field must be at least 1.');

        $response = $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2.5
                ]
            ]
        ]);

        $response->assertStatus(422)->assertJsonPath('message', 'The products.0.quantity field must be an integer.');
    }

    public function testProductCannotBePreparedIfNoIngredientsAvailable()
    {
        $product = Product::factory()->create();
        $ingredient = Product::factory()->create([
            'product_type' => ProductTypes::WEIGHT(),
            'current_stock' => 200,
        ]);

        $product->children()->sync([$ingredient->id => ['quantity' => 200]]);


        $response = $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ])->assertStatus(404);

        $response->assertJsonPath(
            'message', trans('errors.ingredients_finished', ['name' => $ingredient->name])
        );
    }

    public function testProductCreated()
    {
        $product = Product::factory()->create();
        $ingredient = Product::factory()->create([
            'product_type' => ProductTypes::WEIGHT(),
            'current_stock' => 2000,
        ]);
        $product->children()->sync([$ingredient->id => ['quantity' => 200]]);

        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ])
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id', 'total_items_count', 'total_money',
                    'total_money', 'created_at',
                    'order_details' => [
                        [
                            'id', 'order_id', 'product_id', 'product_name',
                            'product_type', 'product_price', 'quantity',
                            'sub_products' => [
                                [
                                    "id", "sku", "name", "price", "description",
                                ]
                            ]
                        ]
                    ],
                ]
            ]);
    }

    public function testStockIsUpdated()
    {
        $product = Product::factory()->create();
        $ingredient = Product::factory()->create([
            'product_type' => ProductTypes::WEIGHT(),
            'current_stock' => 200,
        ]);
        $product->children()->sync([$ingredient->id => ['quantity' => 50]]);

        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $this->assertEquals(100, $ingredient->refresh()->current_stock);

        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);
        $this->assertEquals(0, $ingredient->refresh()->current_stock);
    }

    public function testEmailSentWhenStockIsLow()
    {
        Mail::fake();

        $product = Product::factory()->create();
        $ingredient = Product::factory()->create([
            'product_type' => ProductTypes::WEIGHT(),
            'current_stock' => 300,
        ]);
        $product->children()->sync([$ingredient->id => ['quantity' => 100]]);

        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ])->assertCreated();
        Mail::assertNothingQueued();

        $this->postJson(route('api.orders.store'), [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ])->assertCreated();
        Mail::assertQueued(ProductStockIsLow::class);
        Mail::assertQueuedCount(1);
    }
}
