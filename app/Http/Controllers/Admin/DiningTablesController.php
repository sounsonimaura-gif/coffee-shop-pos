<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\DiningTable;
use App\Models\TableFloor;
use App\Models\TableZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DiningTablesController extends BaseCrudController
{
    protected string $model = DiningTable::class;

    protected string $viewNamespace = 'DiningTables';

    protected string $permissionPrefix = 'dining_tables';

    protected string $routePrefix = 'admin.dining-tables';

    protected string $titleKey = 'coffee.dining_tables';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'table_no',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'capacity',
                'title' => 'coffee.quantity',
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
            'branch_id' => ['required', 'string'],
            'floor_id' => ['nullable', 'string'],
            'zone_id' => ['nullable', 'string'],
            'table_no' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'floors' => TableFloor::query()->select('id', 'name')->orderBy('name')->get(),
            'zones' => TableZone::query()->select('id', 'name')->orderBy('name')->get(),
            'branches' => Branch::query()
                ->when(request()->user()?->company_id, fn ($q, $cid) => $q->where('company_id', $cid))
                ->orderBy('name')->get(['id', 'name']),
        ];
    }

    protected function baseQuery(): Builder
    {
        return DiningTable::query();
    }
}
