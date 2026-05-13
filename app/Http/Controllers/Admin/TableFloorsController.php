<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\TableFloor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TableFloorsController extends BaseCrudController
{
    protected string $model = TableFloor::class;

    protected string $viewNamespace = 'TableFloors';

    protected string $permissionPrefix = 'table_floors';

    protected string $routePrefix = 'admin.table-floors';

    protected string $titleKey = 'coffee.table_floors';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'sort_order',
                'title' => 'coffee.sort_order',
            ],
            2 => [
                'data' => 'is_active',
                'title' => 'coffee.is_active',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'branch_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
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

    protected function baseQuery(): Builder
    {
        return TableFloor::query();
    }
}
