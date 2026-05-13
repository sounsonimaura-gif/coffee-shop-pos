<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionMenuCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'menu_category_id',
    ];

    protected $casts = [
        'promotion_id' => 'integer',
        'menu_category_id' => 'integer',
    ];
}
