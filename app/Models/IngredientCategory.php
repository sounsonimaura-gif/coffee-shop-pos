<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
