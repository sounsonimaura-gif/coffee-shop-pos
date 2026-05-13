<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'ingredient_id',
        'unit_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'received_quantity',
        'unit_cost',
        'discount_amount',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'purchase_id' => 'integer',
        'ingredient_id' => 'integer',
        'unit_id' => 'integer',
        'expiry_date' => 'date',
        'quantity' => 'decimal:4',
        'received_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'line_total' => 'decimal:4',
    ];
}
