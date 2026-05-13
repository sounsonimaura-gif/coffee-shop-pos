<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoriesController extends BaseCrudController
{
    protected string $model = MenuCategory::class;

    protected string $viewNamespace = 'MenuCategories';

    protected string $permissionPrefix = 'menu_categories';

    protected string $routePrefix = 'admin.menu-categories';

    protected string $titleKey = 'coffee.menu_categories';

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
                'data' => 'status',
                'title' => 'coffee.status',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'numeric'],
            'show_on_pos' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
