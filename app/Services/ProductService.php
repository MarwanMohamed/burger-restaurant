<?php

namespace App\Services;

use App\Constants\ProductTypes;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;

class ProductService
{
    public const STOCK_NOTIFICATION_PERCENTAGE = 50;

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function updateStock($product, $quantity): void
    {
        if ($product['product_type'] == ProductTypes::COMBINED()) {
            $this->productRepository->updateCombinedProductQuantity($product, $quantity);
        }
    }

    function checkProductsExist($products, Collection $productsIds): void
    {
        if (count($products->pluck('id')) != count($productsIds)) {
            throw ValidationException::withMessages([
                'product_id' => trans('errors.product_not_exist_anymore')
            ]);
        }
    }

    function checkQuantity($product, $productsIdsAndQuantities)
    {
        if ($product['product_type'] == ProductTypes::COMBINED()) {

            $productsQuery = DB::select('select id, current_stock, name from products where id in ( ' . $product->children->pluck('id')->implode(',') . ')');

            foreach ($product->children as $child) {
                $quantity = $productsIdsAndQuantities;
                $grams = $quantity * $child->pivot->quantity;
                foreach ($productsQuery as $productResult) {
                    if (($child->id == $productResult->id) && $productResult->current_stock < $grams) {
                        return $child->name;
                    }
                }

                return true;
            }
        }
        return false;
    }
}
