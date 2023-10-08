<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ProductService;

class ProductObserver
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function creating(Product $product)
    {
        if ($product->current_stock) {
            $product->stock_notification_limit =
                ($product->current_stock * $this->productService::STOCK_NOTIFICATION_PERCENTAGE) / 100;
        }
    }
}
