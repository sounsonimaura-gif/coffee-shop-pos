<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'ingredient_id',
        'supplier_id',
        'purchase_item_id',
        'batch_no',
        'expiry_date',
        'initial_quantity',
        'current_quantity',
        'unit_cost',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'ingredient_id' => 'integer',
        'supplier_id' => 'integer',
        'purchase_item_id' => 'integer',
        'expiry_date' => 'date',
        'initial_quantity' => 'decimal:4',
        'current_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
    ];
}
