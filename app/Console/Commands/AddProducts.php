<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AddProducts extends Command
{
    protected $signature = 'add-products';

    protected $description = 'Seed Products';

    public function handle()
    {
        try {
            Excel::import(new ProductsImport, Storage::path('public/Products.csv'));

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}
