<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'unit_id',
        'default_supplier_id',
        'ingredient_code',
        'name',
        'item_type',
        'cost_price',
        'minimum_stock',
        'expiry_alert_days',
        'track_expiry',
        'track_batch',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'category_id' => 'integer',
        'unit_id' => 'integer',
        'default_supplier_id' => 'integer',
        'cost_price' => 'decimal:4',
        'minimum_stock' => 'decimal:4',
        'expiry_alert_days' => 'integer',
        'track_expiry' => 'boolean',
        'track_batch' => 'boolean',
    ];
}
