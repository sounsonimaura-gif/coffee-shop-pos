<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'order_id',
        'delivery_staff_id',
        'delivery_no',
        'delivery_address',
        'receiver_name',
        'receiver_phone',
        'delivery_fee',
        'distance_km',
        'status',
        'picked_up_at',
        'delivered_at',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'order_id' => 'integer',
        'delivery_staff_id' => 'integer',
        'delivery_fee' => 'decimal:4',
        'distance_km' => 'decimal:4',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
}
