<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'group',
        'key',
        'value',
        'value_type',
        'is_public',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'is_public' => 'boolean',
    ];
}
