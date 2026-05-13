<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\PosCounter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PosCountersController extends BaseCrudController
{
    protected string $model = PosCounter::class;

    protected string $viewNamespace = 'PosCounters';

    protected string $permissionPrefix = 'pos_counters';

    protected string $routePrefix = 'admin.pos-counters';

    protected string $titleKey = 'coffee.pos_counters';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'counter_code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'status',
                'title' => 'coffee.status',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'branch_id' => ['required', 'string'],
            'counter_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'printer_name' => ['nullable', 'string', 'max:255'],
            'receipt_size' => ['nullable', 'string'],
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
        return PosCounter::query();
    }
}
