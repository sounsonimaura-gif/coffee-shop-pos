<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\MenuSize;
use Illuminate\Http\Request;

class MenuSizesController extends BaseCrudController
{
    protected string $model = MenuSize::class;

    protected string $viewNamespace = 'MenuSizes';

    protected string $permissionPrefix = 'menu_sizes';

    protected string $routePrefix = 'admin.menu-sizes';

    protected string $titleKey = 'coffee.menu_sizes';

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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
