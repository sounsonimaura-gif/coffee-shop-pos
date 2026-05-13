<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\MenuAddon;
use Illuminate\Http\Request;

class MenuAddonsController extends BaseCrudController
{
    protected string $model = MenuAddon::class;

    protected string $viewNamespace = 'MenuAddons';

    protected string $permissionPrefix = 'menu_addons';

    protected string $routePrefix = 'admin.menu-addons';

    protected string $titleKey = 'coffee.menu_addons';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'price',
                'title' => 'coffee.price',
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
            'price' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
