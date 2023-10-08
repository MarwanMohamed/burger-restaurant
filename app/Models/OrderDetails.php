<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'product_name', 'product_type', 'product_price', 'quantity', 'sub_products'];
    protected $table = 'orders_details';
    protected $casts = ['sub_products' => 'array'];
}
