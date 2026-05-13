<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class IngredientsController extends BaseCrudController
{
    protected string $model = Ingredient::class;

    protected string $viewNamespace = 'Ingredients';

    protected string $permissionPrefix = 'ingredients';

    protected string $routePrefix = 'admin.ingredients';

    protected string $titleKey = 'coffee.ingredients';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'ingredient_code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
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
            'category_id' => ['nullable', 'string'],
            'unit_id' => ['nullable', 'string'],
            'ingredient_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'min_stock_level' => ['nullable', 'numeric'],
            'reorder_level' => ['nullable', 'numeric'],
            'cost_per_unit' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'ingredient_categories' => IngredientCategory::query()->select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::query()->select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
