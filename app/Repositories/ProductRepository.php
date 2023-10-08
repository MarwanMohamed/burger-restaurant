<?php

namespace App\Repositories;

use App\Models\Product;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductRepository
{
    public function getProducts($productsIds)
    {
        return Product::whereIn('id', $productsIds)->with('children')->get();
    }

    public function updateCombinedProductQuantity($product, $quantity): void
    {
        $product->children->each(function ($child) use ($quantity) {
            $grams = $quantity * $child->pivot->quantity;
            $updateStockAffected = DB::update('update products set current_stock = current_stock - ' . $grams
                . ' where current_stock  >= ' . $grams . ' and id = ' . $child->id);

            if (1 !== $updateStockAffected) {
                throw new ModelNotFoundException(
                    trans('errors.ingredients_finished', ['name' => $child->name])
                );
            }
        });
    }
}
