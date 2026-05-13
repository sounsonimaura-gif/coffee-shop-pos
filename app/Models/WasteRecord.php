<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'ingredient_id',
        'stock_batch_id',
        'created_by',
        'quantity',
        'cost_amount',
        'waste_type',
        'reason',
        'waste_date',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'ingredient_id' => 'integer',
        'stock_batch_id' => 'integer',
        'created_by' => 'integer',
        'quantity' => 'decimal:4',
        'cost_amount' => 'decimal:4',
        'waste_date' => 'date',
    ];
}
