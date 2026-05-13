<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'company_id',
        'coupon_code',
        'discount_type',
        'discount_value',
        'minimum_purchase_amount',
        'usage_limit',
        'used_count',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'promotion_id' => 'integer',
        'company_id' => 'integer',
        'discount_value' => 'decimal:4',
        'minimum_purchase_amount' => 'decimal:4',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
