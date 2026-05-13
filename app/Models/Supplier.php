<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'supplier_code',
        'name',
        'phone',
        'email',
        'address',
        'contact_person',
        'tax_no',
        'opening_balance',
        'credit_limit',
        'credit_days',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'opening_balance' => 'decimal:4',
        'credit_limit' => 'decimal:4',
        'credit_days' => 'integer',
    ];
}
