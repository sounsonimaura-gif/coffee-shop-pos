<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'branch_id',
        'branch_price',
        'availability_status',
    ];

    protected $casts = [
        'menu_item_id' => 'integer',
        'branch_id' => 'integer',
        'branch_price' => 'decimal:4',
    ];
}
