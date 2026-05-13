<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_size_id',
        'kitchen_station_id',
        'item_name',
        'size_name',
        'quantity',
        'unit_price',
        'addon_amount',
        'discount_amount',
        'tax_amount',
        'line_total',
        'kitchen_status',
        'special_note',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'menu_item_id' => 'integer',
        'menu_size_id' => 'integer',
        'kitchen_station_id' => 'integer',
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'addon_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'line_total' => 'decimal:4',
    ];
}
