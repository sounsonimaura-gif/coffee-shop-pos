<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'min_spend',
        'min_points',
        'discount_percent',
        'point_multiplier',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'min_spend' => 'decimal:4',
        'min_points' => 'integer',
        'discount_percent' => 'decimal:4',
        'point_multiplier' => 'decimal:4',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
