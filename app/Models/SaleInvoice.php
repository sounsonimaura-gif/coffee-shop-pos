<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'order_id',
        'customer_id',
        'cashier_shift_id',
        'cashier_id',
        'sale_no',
        'sale_at',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'service_charge_amount',
        'delivery_fee',
        'grand_total',
        'paid_amount',
        'change_amount',
        'status',
        'qr_payment_reference',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'order_id' => 'integer',
        'customer_id' => 'integer',
        'cashier_shift_id' => 'integer',
        'cashier_id' => 'integer',
        'sale_at' => 'datetime',
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'service_charge_amount' => 'decimal:4',
        'delivery_fee' => 'decimal:4',
        'grand_total' => 'decimal:4',
        'paid_amount' => 'decimal:4',
        'change_amount' => 'decimal:4',
    ];
}
