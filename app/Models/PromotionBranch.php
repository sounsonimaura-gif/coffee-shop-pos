<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'branch_id',
    ];

    protected $casts = [
        'promotion_id' => 'integer',
        'branch_id' => 'integer',
    ];
}
