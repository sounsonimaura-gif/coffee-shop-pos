<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\ReportExport;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;

class ReportExportsController extends BaseReadOnlyController
{
    protected string $model = ReportExport::class;

    protected string $viewNamespace = 'ReportExports';

    protected string $permissionPrefix = 'report_exports';

    protected string $routePrefix = 'admin.report-exports';

    protected string $titleKey = 'coffee.report_exports';

    protected function columns(): array
    {
        return [
            ['data' => 'created_at', 'title' => 'coffee.date'],
            ['data' => 'report_type', 'title' => 'coffee.export_type'],
            ['data' => 'file_type', 'title' => 'coffee.format'],
            ['data' => 'file_path', 'title' => 'coffee.file_name'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function baseQuery(): Builder
    {
        $q = ReportExport::query();
        $companyId = request()->user()?->company_id;
        if ($companyId) {
            $q->where('company_id', $companyId);
        }

        return $q;
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt
            ->editColumn('created_at', fn ($r) => optional($r->created_at)?->format('Y-m-d H:i'))
            ->editColumn('status', function ($r) {
                $map = [
                    'completed' => 'bg-success',
                    'pending' => 'bg-warning',
                    'processing' => 'bg-info',
                    'failed' => 'bg-danger',
                ];
                $cls = $map[$r->status] ?? 'bg-secondary';

                return '<span class="badge '.$cls.'">'.e(__('coffee.'.$r->status)).'</span>';
            })
            ->rawColumns(['status']);
    }
}
