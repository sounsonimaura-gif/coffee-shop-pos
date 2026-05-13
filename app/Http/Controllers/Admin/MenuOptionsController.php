<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\MenuOption;
use Illuminate\Http\Request;

class MenuOptionsController extends BaseCrudController
{
    protected string $model = MenuOption::class;

    protected string $viewNamespace = 'MenuOptions';

    protected string $permissionPrefix = 'menu_options';

    protected string $routePrefix = 'admin.menu-options';

    protected string $titleKey = 'coffee.menu_options';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'is_required',
                'title' => 'coffee.is_active',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'allow_multiple' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'numeric'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
