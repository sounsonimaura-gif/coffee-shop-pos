<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'from_branch_id',
        'to_branch_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'created_by',
        'approved_by',
        'received_by',
        'transfer_no',
        'transfer_date',
        'status',
        'note',
        'approved_at',
        'sent_at',
        'received_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'from_branch_id' => 'integer',
        'to_branch_id' => 'integer',
        'from_warehouse_id' => 'integer',
        'to_warehouse_id' => 'integer',
        'created_by' => 'integer',
        'approved_by' => 'integer',
        'received_by' => 'integer',
        'transfer_date' => 'date',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];
}
