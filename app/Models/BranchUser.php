<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BranchUser extends Pivot
{
    protected $table = 'branch_user';

    public $incrementing = true;

    protected $fillable = [
        'branch_id',
        'user_id',
        'role_id',
        'is_default',
        'can_access',
    ];

    protected $casts = [
        'branch_id' => 'integer',
        'user_id' => 'integer',
        'role_id' => 'integer',
        'is_default' => 'boolean',
        'can_access' => 'boolean',
    ];
}
