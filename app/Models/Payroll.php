<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'staff_id',
        'payment_method_id',
        'payroll_no',
        'period_month',
        'basic_salary',
        'commission_amount',
        'bonus_amount',
        'deduction_amount',
        'net_salary',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'staff_id' => 'integer',
        'payment_method_id' => 'integer',
        'basic_salary' => 'decimal:4',
        'commission_amount' => 'decimal:4',
        'bonus_amount' => 'decimal:4',
        'deduction_amount' => 'decimal:4',
        'net_salary' => 'decimal:4',
        'payment_date' => 'date',
    ];
}
