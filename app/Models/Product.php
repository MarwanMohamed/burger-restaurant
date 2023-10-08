<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'product_type', 'sku', 'current_stock',
        'stock_notification_limit', 'stock_notification_started_at', 'stock_notification_sent_at'
    ];

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_combined_children', 'product_id', 'child_product_id')
            ->withPivot('quantity');
    }
}
