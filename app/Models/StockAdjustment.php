<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    protected $fillable = [
        'company_id',
        'branch_id',
        'warehouse_id',
        'ingredient_id',
        'stock_batch_id',
        'created_by',
        'adjustment_type',
        'quantity',
        'reason',
        'note',
        'adjusted_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'warehouse_id' => 'integer',
        'ingredient_id' => 'integer',
        'stock_batch_id' => 'integer',
        'created_by' => 'integer',
        'quantity' => 'decimal:4',
        'adjusted_at' => 'datetime',
    ];
}
