<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'sale_invoice_id',
        'payment_method_id',
        'received_by',
        'payment_no',
        'payment_for',
        'amount',
        'change_amount',
        'reference_no',
        'paid_at',
        'status',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'sale_invoice_id' => 'integer',
        'payment_method_id' => 'integer',
        'received_by' => 'integer',
        'amount' => 'decimal:4',
        'change_amount' => 'decimal:4',
        'paid_at' => 'datetime',
    ];
}
