<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'requested_by',
        'report_type',
        'filters',
        'file_type',
        'file_path',
        'status',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'branch_id' => 'integer',
        'requested_by' => 'integer',
    ];
}
