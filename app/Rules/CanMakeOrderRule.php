<?php

namespace App\Rules;

use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CanMakeOrderRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $productService = app(ProductService::class);
        $productRepository = app(ProductRepository::class);

        $productsIdsAndQuantities = collect($value)->pluck('quantity', 'product_id');
        $productsIds = $productsIdsAndQuantities->keys();

        $products = $productRepository->getProducts($productsIds);
        $productService->checkProductsExist($products, $productsIds);

        collect($products)->each(function ($product) use ($productsIdsAndQuantities, $fail, $productService) {
            $productHasQuantity = $productService->checkQuantity($product, $productsIdsAndQuantities[$product->id]);

            if (!$productHasQuantity)
                $fail(trans('errors.ingredients_finished'));
        });

    }
}
