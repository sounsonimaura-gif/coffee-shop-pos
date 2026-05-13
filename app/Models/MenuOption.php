<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'option_group',
        'name',
        'price_modifier',
        'sort_order',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'price_modifier' => 'decimal:4',
        'sort_order' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];
}
