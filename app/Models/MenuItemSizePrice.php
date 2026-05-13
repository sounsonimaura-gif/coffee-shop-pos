<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemSizePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'menu_size_id',
        'price',
        'is_default',
    ];

    protected $casts = [
        'menu_item_id' => 'integer',
        'menu_size_id' => 'integer',
        'price' => 'decimal:4',
        'is_default' => 'boolean',
    ];
}
