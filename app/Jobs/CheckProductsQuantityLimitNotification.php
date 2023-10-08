<?php

namespace App\Jobs;

use App\Constants\ProductTypes;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckProductsQuantityLimitNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productIds;

    public function __construct($productIds)
    {
        $this->productIds = $productIds;
    }

    public function handle(): void
    {
        $products = Product::whereIn('id', $this->productIds)->with('children')->get();
        foreach ($products as $product) {
            if ($product->product_type == ProductTypes::COMBINED()) {
                $checkedSubProduct = [];
                foreach ($product->children as $child) {
                    if ($child->current_stock <= $child->stock_notification_limit) {
                        if (!in_array($child->id, $checkedSubProduct)) {
                            $checkedSubProduct[] = $child->id;
                            dispatch(new CheckProductQuantityLimitAndSendEmailNotification($child));
                        }
                    }
                }
            }
        }
    }
}
