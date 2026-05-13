<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'promotion_code',
        'name',
        'promotion_type',
        'discount_type',
        'discount_value',
        'minimum_purchase_amount',
        'usage_limit',
        'used_count',
        'start_at',
        'end_at',
        'happy_hour_start',
        'happy_hour_end',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'discount_value' => 'decimal:4',
        'minimum_purchase_amount' => 'decimal:4',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
