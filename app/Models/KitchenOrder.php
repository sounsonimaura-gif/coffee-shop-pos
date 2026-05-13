<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'kitchen_station_id',
        'ticket_no',
        'status',
        'sent_at',
        'started_at',
        'ready_at',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'kitchen_station_id' => 'integer',
        'sent_at' => 'datetime',
        'started_at' => 'datetime',
        'ready_at' => 'datetime',
    ];
}
