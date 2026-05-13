<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'station_code',
        'name',
        'station_type',
        'printer_name',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'branch_id' => 'integer',
        'sort_order' => 'integer',
    ];
}
