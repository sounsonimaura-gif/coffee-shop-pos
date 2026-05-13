<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'menu_size_id',
        'name',
        'estimated_cost',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'menu_item_id' => 'integer',
        'menu_size_id' => 'integer',
        'estimated_cost' => 'decimal:4',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
