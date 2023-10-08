<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetails;

class OrderRepository
{
    public function store($products, $productsIdsAndQuantities)
    {
        $order = Order::create([
            'total_items_count' => count($productsIdsAndQuantities),
            'total_money' => rand(100, 10000),
            'status' => 'IN_PROGRESS' //should be Enum
        ]);

        foreach ($products as $product) {
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_type' => $product->product_type,
                'product_price' => $product->price,
                'quantity' => $productsIdsAndQuantities[$product->id],
                'sub_products' => $product->children->each(function ($child) {return $child; })
            ]);
        }

        return $order->with('orderDetails')->first();
    }
}
