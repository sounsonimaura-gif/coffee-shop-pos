<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'ingredient_id',
        'stock_batch_id',
        'created_by',
        'movement_type',
        'reference_type',
        'reference_id',
        'reference_no',
        'quantity_in',
        'quantity_out',
        'balance_after',
        'unit_cost',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'ingredient_id' => 'integer',
        'stock_batch_id' => 'integer',
        'created_by' => 'integer',
        'reference_id' => 'integer',
        'quantity_in' => 'decimal:4',
        'quantity_out' => 'decimal:4',
        'balance_after' => 'decimal:4',
        'unit_cost' => 'decimal:4',
    ];
}
