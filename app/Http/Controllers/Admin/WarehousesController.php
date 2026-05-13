<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehousesController extends BaseCrudController
{
    protected string $model = Warehouse::class;

    protected string $viewNamespace = 'Warehouses';

    protected string $permissionPrefix = 'warehouses';

    protected string $routePrefix = 'admin.warehouses';

    protected string $titleKey = 'coffee.warehouses';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'warehouse_code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'is_default',
                'title' => 'coffee.is_active',
            ],
            3 => [
                'data' => 'status',
                'title' => 'coffee.status',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'branch_id' => ['nullable', 'string'],
            'warehouse_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'branches' => Branch::query()
                ->when(request()->user()?->company_id, fn ($q, $cid) => $q->where('company_id', $cid))
                ->orderBy('name')->get(['id', 'name']),
        ];
    }
}
