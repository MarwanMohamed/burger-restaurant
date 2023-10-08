<?php

namespace App\Services;

use App\Jobs\CheckProductsQuantityLimitNotification;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private ProductService $productService;

    public function __construct(
        ProductService    $productService,
        OrderRepository   $orderRepository,
        ProductRepository $productRepository
    )
    {
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    public function createOrder($productsArray)
    {
        $productsIdsAndQuantities = collect($productsArray)->pluck('quantity', 'product_id');
        $productsIds = $productsIdsAndQuantities->keys();

        $order = DB::transaction(function () use ($productsArray, $productsIds, $productsIdsAndQuantities) {
            $products = $this->productRepository->getProducts($productsIds);
            $this->productService->checkProductsExist($products, $productsIds);

            collect($products)->each(function ($product) use ($productsIdsAndQuantities) {
                $quantity = $productsIdsAndQuantities[$product->id];
                $this->productService->updateStock($product, $quantity);
            });
            return $this->orderRepository->store($products, $productsIdsAndQuantities);
        });

        dispatch(new CheckProductsQuantityLimitNotification($productsIds));

        return $order;
    }
}
