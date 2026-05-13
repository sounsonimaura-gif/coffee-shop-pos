<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'order_id',
        'online_order_no',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'subtotal',
        'delivery_fee',
        'grand_total',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'customer_id' => 'integer',
        'order_id' => 'integer',
        'subtotal' => 'decimal:4',
        'delivery_fee' => 'decimal:4',
        'grand_total' => 'decimal:4',
    ];
}
