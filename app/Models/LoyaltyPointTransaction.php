<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'sale_invoice_id',
        'transaction_type',
        'points',
        'balance_after',
        'amount_value',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'customer_id' => 'integer',
        'sale_invoice_id' => 'integer',
        'points' => 'integer',
        'balance_after' => 'integer',
        'amount_value' => 'decimal:4',
    ];
}
