<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'floor_id',
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'branch_id' => 'integer',
        'floor_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
