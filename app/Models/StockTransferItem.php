<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasFactory;

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    protected $fillable = [
        'stock_transfer_id',
        'ingredient_id',
        'stock_batch_id',
        'unit_id',
        'quantity_requested',
        'quantity_sent',
        'quantity_received',
        'note',
    ];

    protected $casts = [
        'stock_transfer_id' => 'integer',
        'ingredient_id' => 'integer',
        'stock_batch_id' => 'integer',
        'unit_id' => 'integer',
        'quantity_requested' => 'decimal:4',
        'quantity_sent' => 'decimal:4',
        'quantity_received' => 'decimal:4',
    ];
}
