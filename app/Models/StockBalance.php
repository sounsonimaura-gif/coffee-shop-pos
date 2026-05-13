<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'ingredient_id',
        'quantity_on_hand',
        'reserved_quantity',
        'available_quantity',
        'average_cost',
        'stock_value',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'ingredient_id' => 'integer',
        'quantity_on_hand' => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
        'available_quantity' => 'decimal:4',
        'average_cost' => 'decimal:4',
        'stock_value' => 'decimal:4',
    ];
}
