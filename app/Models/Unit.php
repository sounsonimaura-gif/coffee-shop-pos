<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'symbol',
        'base_unit_multiplier',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'base_unit_multiplier' => 'decimal:4',
        'is_active' => 'boolean',
    ];
}
