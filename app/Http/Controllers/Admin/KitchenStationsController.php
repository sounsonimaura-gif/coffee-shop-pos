<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\KitchenStation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class KitchenStationsController extends BaseCrudController
{
    protected string $model = KitchenStation::class;

    protected string $viewNamespace = 'KitchenStations';

    protected string $permissionPrefix = 'kitchen_stations';

    protected string $routePrefix = 'admin.kitchen-stations';

    protected string $titleKey = 'coffee.kitchen_stations';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'station_code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'station_type',
                'title' => 'coffee.type',
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
            'station_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'station_type' => ['nullable', 'string'],
            'printer_name' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'numeric'],
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

    protected function baseQuery(): Builder
    {
        return KitchenStation::query();
    }
}
