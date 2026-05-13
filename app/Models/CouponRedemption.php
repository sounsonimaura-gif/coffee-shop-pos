<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'customer_id',
        'sale_invoice_id',
        'discount_amount',
    ];

    protected $casts = [
        'coupon_id' => 'integer',
        'customer_id' => 'integer',
        'sale_invoice_id' => 'integer',
        'discount_amount' => 'decimal:4',
    ];
}
