<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitsController extends BaseCrudController
{
    protected string $model = Unit::class;

    protected string $viewNamespace = 'Units';

    protected string $permissionPrefix = 'units';

    protected string $routePrefix = 'admin.units';

    protected string $titleKey = 'coffee.units';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'symbol',
                'title' => 'coffee.code',
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
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:255'],
            'base_unit_multiplier' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
