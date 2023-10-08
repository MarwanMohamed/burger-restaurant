<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_items_count', 'total_money', 'status'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
