<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\DatabaseBackup;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;

class DatabaseBackupsController extends BaseReadOnlyController
{
    protected string $model = DatabaseBackup::class;

    protected string $viewNamespace = 'DatabaseBackups';

    protected string $permissionPrefix = 'database_backups';

    protected string $routePrefix = 'admin.database-backups';

    protected string $titleKey = 'coffee.database_backups';

    protected function columns(): array
    {
        return [
            ['data' => 'created_at', 'title' => 'coffee.date'],
            ['data' => 'backup_name', 'title' => 'coffee.file_name'],
            ['data' => 'backup_type', 'title' => 'coffee.type'],
            ['data' => 'file_size', 'title' => 'coffee.file_size'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function baseQuery(): Builder
    {
        $q = DatabaseBackup::query();
        $companyId = request()->user()?->company_id;
        if ($companyId) {
            $q->where('company_id', $companyId);
        }

        return $q;
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt
            ->editColumn('created_at', fn ($b) => optional($b->created_at)?->format('Y-m-d H:i'))
            ->editColumn('file_size', fn ($b) => $this->formatBytes($b->file_size ?? 0))
            ->editColumn('status', function ($b) {
                $map = [
                    'completed' => 'bg-success',
                    'pending' => 'bg-warning',
                    'failed' => 'bg-danger',
                ];
                $cls = $map[$b->status] ?? 'bg-secondary';

                return '<span class="badge '.$cls.'">'.e(__('coffee.'.$b->status)).'</span>';
            })
            ->rawColumns(['status']);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return number_format($bytes, 2).' '.$units[$i];
    }
}
