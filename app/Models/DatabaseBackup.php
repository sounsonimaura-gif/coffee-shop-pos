<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by',
        'backup_name',
        'file_path',
        'file_size',
        'backup_type',
        'status',
        'backup_at',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'created_by' => 'integer',
        'file_size' => 'integer',
        'backup_at' => 'datetime',
    ];
}
