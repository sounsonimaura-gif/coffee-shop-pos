<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\StockAlert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\DataTableAbstract;

class StockAlertsController extends BaseReadOnlyController
{
    protected string $model = StockAlert::class;

    protected string $viewNamespace = 'StockAlerts';

    protected string $permissionPrefix = 'stock_alerts';

    protected string $routePrefix = 'admin.stock-alerts';

    protected string $titleKey = 'coffee.stock_alerts';

    protected function columns(): array
    {
        return [
            ['data' => 'created_at', 'title' => 'coffee.date'],
            ['data' => 'alert_type', 'title' => 'coffee.type'],
            ['data' => 'ingredient_name', 'name' => 'ingredient.name', 'title' => 'coffee.ingredient'],
            ['data' => 'current_quantity', 'title' => 'coffee.current_quantity'],
            ['data' => 'expiry_date', 'title' => 'coffee.expiry_date'],
            ['data' => 'is_resolved', 'title' => 'coffee.acknowledged'],
            ['data' => 'actions', 'title' => 'coffee.actions', 'orderable' => false, 'searchable' => false],
        ];
    }

    protected function baseQuery(): Builder
    {
        $q = StockAlert::query()->with('ingredient');
        $companyId = request()->user()?->company_id;
        if ($companyId) {
            $q->where('company_id', $companyId);
        }

        return $q;
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt
            ->addColumn('ingredient_name', fn ($a) => $a->ingredient?->name ?? '-')
            ->editColumn('created_at', fn ($a) => optional($a->created_at)?->format('Y-m-d H:i'))
            ->editColumn('is_resolved', fn ($a) => $a->is_resolved
                ? '<span class="badge bg-success">'.__('coffee.yes').'</span>'
                : '<span class="badge bg-warning">'.__('coffee.no').'</span>'
            )
            ->addColumn('actions', function ($a) {
                if ($a->is_resolved) {
                    return '';
                }
                $url = route('admin.stock-alerts.acknowledge', $a->id);

                return '<button type="button" class="btn btn-sm btn-success js-stock-alert-ack" data-url="'.$url.'">'
                    .'<i class="bi bi-check2"></i> '.__('coffee.acknowledge').'</button>';
            })
            ->rawColumns(['is_resolved', 'actions']);
    }

    public function acknowledge(int $id): RedirectResponse
    {
        $this->authorizeView();
        $alert = $this->baseQuery()->findOrFail($id);
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);

        return back()->with('success', __('coffee.acknowledged_successfully'));
    }
}
