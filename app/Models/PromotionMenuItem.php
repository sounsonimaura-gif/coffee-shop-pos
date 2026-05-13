<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionMenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'menu_item_id',
    ];

    protected $casts = [
        'promotion_id' => 'integer',
        'menu_item_id' => 'integer',
    ];
}
