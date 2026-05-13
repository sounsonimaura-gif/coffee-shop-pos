<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'staff_code',
        'name',
        'phone',
        'email',
        'position',
        'salary',
        'hire_date',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'user_id' => 'integer',
        'salary' => 'decimal:4',
        'hire_date' => 'date',
    ];
}
