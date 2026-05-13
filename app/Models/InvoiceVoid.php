<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceVoid extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_invoice_id',
        'voided_by',
        'reason',
        'voided_at',
    ];

    protected $casts = [
        'sale_invoice_id' => 'integer',
        'voided_by' => 'integer',
        'voided_at' => 'datetime',
    ];
}
