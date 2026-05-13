<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'branch_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_day_off',
    ];

    protected $casts = [
        'staff_id' => 'integer',
        'branch_id' => 'integer',
        'day_of_week' => 'integer',
        'is_day_off' => 'boolean',
    ];
}
