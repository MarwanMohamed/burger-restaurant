<?php

namespace App\Imports;

use App\Models\CombinedProductChild;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductsImport implements ToModel, WithStartRow
{
    use RemembersRowNumber;

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        //must make validation here
        $productRow = [
            'name' => $row[0],
            'description' => $row[1],
            'product_type' => $row[2],
            'sku' => $row[3],
            'current_stock' => $row[4],
            'quantity' => $row[5],
            'product_id' => $row[6]
        ];
        $product = Product::firstOrCreate(['name' => $row[0]], collect($productRow)->except('quantity', 'product_id')->toArray());

        if (isset($row[6])) {

            CombinedProductChild::firstOrCreate([
                'child_product_id' => $product->id,
                'quantity' => $row[5],
                'product_id' => $row[6]
            ]);
        }

        return $product;
    }
}
