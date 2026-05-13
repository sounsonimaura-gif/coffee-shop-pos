<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\IngredientCategory;
use Illuminate\Http\Request;

class IngredientCategoriesController extends BaseCrudController
{
    protected string $model = IngredientCategory::class;

    protected string $viewNamespace = 'IngredientCategories';

    protected string $permissionPrefix = 'ingredient_categories';

    protected string $routePrefix = 'admin.ingredient-categories';

    protected string $titleKey = 'coffee.ingredient_categories';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'is_active',
                'title' => 'coffee.is_active',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
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
