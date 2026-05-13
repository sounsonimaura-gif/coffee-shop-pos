<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'price_modifier',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'price_modifier' => 'decimal:4',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
