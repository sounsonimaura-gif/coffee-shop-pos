<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'unit_id',
        'quantity_used',
        'cost_per_unit',
        'total_cost',
        'is_required',
        'deduct_stock',
        'note',
    ];

    protected $casts = [
        'recipe_id' => 'integer',
        'ingredient_id' => 'integer',
        'unit_id' => 'integer',
        'quantity_used' => 'decimal:4',
        'cost_per_unit' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'is_required' => 'boolean',
        'deduct_stock' => 'boolean',
    ];
}
