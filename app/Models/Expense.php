<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'expense_category_id',
        'payment_method_id',
        'created_by',
        'expense_no',
        'expense_date',
        'amount',
        'receipt_path',
        'reference_no',
        'note',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'expense_category_id' => 'integer',
        'payment_method_id' => 'integer',
        'created_by' => 'integer',
        'expense_date' => 'date',
        'amount' => 'decimal:4',
    ];
}
