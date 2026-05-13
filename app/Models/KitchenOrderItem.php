<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kitchen_order_id',
        'order_item_id',
        'status',
    ];

    protected $casts = [
        'kitchen_order_id' => 'integer',
        'order_item_id' => 'integer',
    ];
}
