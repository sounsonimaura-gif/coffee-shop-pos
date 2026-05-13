<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'account_no',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}
