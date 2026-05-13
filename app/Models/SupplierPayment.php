<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'supplier_id',
        'purchase_id',
        'payment_method_id',
        'paid_by',
        'payment_no',
        'amount',
        'payment_date',
        'reference_no',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'supplier_id' => 'integer',
        'purchase_id' => 'integer',
        'payment_method_id' => 'integer',
        'paid_by' => 'integer',
        'amount' => 'decimal:4',
        'payment_date' => 'date',
    ];
}
