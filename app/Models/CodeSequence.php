<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'module',
        'prefix',
        'suffix',
        'next_number',
        'padding',
        'date_format',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'next_number' => 'integer',
        'padding' => 'integer',
    ];
}
