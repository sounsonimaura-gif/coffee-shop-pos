<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'counter_code',
        'name',
        'printer_name',
        'receipt_size',
        'status',
    ];

    protected $casts = [
        'branch_id' => 'integer',
    ];
}
