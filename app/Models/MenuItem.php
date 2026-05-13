<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'kitchen_station_id',
        'menu_code',
        'name',
        'slug',
        'image_path',
        'description',
        'base_price',
        'cost_price',
        'sale_price',
        'preparation_time_minutes',
        'track_recipe_stock',
        'is_featured',
        'is_best_seller',
        'availability_status',
        'available_from',
        'available_to',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'category_id' => 'integer',
        'kitchen_station_id' => 'integer',
        'base_price' => 'decimal:4',
        'cost_price' => 'decimal:4',
        'sale_price' => 'decimal:4',
        'preparation_time_minutes' => 'integer',
        'track_recipe_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
    ];
}
