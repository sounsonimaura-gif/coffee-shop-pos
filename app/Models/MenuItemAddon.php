<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'menu_addon_id',
    ];

    protected $casts = [
        'menu_item_id' => 'integer',
        'menu_addon_id' => 'integer',
    ];
}
