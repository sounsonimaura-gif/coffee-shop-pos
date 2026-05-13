<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'slug',
        'image_path',
        'sort_order',
        'show_on_pos',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'show_on_pos' => 'boolean',
    ];
}
