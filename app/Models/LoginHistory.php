<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'is_success',
        'failure_reason',
        'logged_in_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_success' => 'boolean',
        'logged_in_at' => 'datetime',
    ];
}
