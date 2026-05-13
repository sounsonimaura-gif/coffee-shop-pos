<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'membership_level_id',
        'customer_code',
        'name',
        'phone',
        'email',
        'gender',
        'dob',
        'address',
        'customer_type',
        'point_balance',
        'total_spent',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'membership_level_id' => 'integer',
        'dob' => 'date',
        'point_balance' => 'integer',
        'total_spent' => 'decimal:4',
    ];
}
