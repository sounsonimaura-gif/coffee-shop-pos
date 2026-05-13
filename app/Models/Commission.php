<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'staff_id',
        'sale_invoice_id',
        'commission_type',
        'base_amount',
        'rate',
        'commission_amount',
        'commission_date',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'staff_id' => 'integer',
        'sale_invoice_id' => 'integer',
        'base_amount' => 'decimal:4',
        'rate' => 'decimal:4',
        'commission_amount' => 'decimal:4',
        'commission_date' => 'date',
    ];
}
