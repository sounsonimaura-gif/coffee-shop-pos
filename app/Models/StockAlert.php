<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'ingredient_id',
        'stock_batch_id',
        'alert_type',
        'current_quantity',
        'expiry_date',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'ingredient_id' => 'integer',
        'stock_batch_id' => 'integer',
        'current_quantity' => 'decimal:4',
        'expiry_date' => 'date',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];
}
