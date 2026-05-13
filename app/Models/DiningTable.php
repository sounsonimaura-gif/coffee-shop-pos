<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiningTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'floor_id',
        'zone_id',
        'table_code',
        'name',
        'seat_count',
        'status',
        'position_x',
        'position_y',
    ];

    protected $casts = [
        'branch_id' => 'integer',
        'floor_id' => 'integer',
        'zone_id' => 'integer',
        'seat_count' => 'integer',
        'position_x' => 'integer',
        'position_y' => 'integer',
    ];
}
