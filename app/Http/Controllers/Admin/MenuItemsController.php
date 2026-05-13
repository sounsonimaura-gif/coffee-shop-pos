<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemsController extends BaseCrudController
{
    protected string $model = MenuItem::class;

    protected string $viewNamespace = 'MenuItems';

    protected string $permissionPrefix = 'menu_items';

    protected string $routePrefix = 'admin.menu-items';

    protected string $titleKey = 'coffee.menu_items';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'menu_code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'sale_price',
                'title' => 'coffee.price',
            ],
            3 => [
                'data' => 'availability_status',
                'title' => 'coffee.status',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'category_id' => ['nullable', 'string'],
            'menu_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric'],
            'cost_price' => ['nullable', 'numeric'],
            'sale_price' => ['nullable', 'numeric'],
            'preparation_time_minutes' => ['nullable', 'numeric'],
            'is_featured' => ['nullable', 'boolean'],
            'is_best_seller' => ['nullable', 'boolean'],
            'availability_status' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'menu_categories' => MenuCategory::query()->select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
