<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CombinedProductChild extends Model
{
    use HasFactory;

    protected $fillable = ['child_product_id', 'quantity', 'product_id'];
    protected $table = 'products_combined_children';

    public function childProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'child_product_id');
    }
}
