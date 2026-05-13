<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'price',
        'cost_price',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'price' => 'decimal:4',
        'cost_price' => 'decimal:4',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
