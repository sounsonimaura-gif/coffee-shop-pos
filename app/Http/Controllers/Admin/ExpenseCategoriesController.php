<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoriesController extends BaseCrudController
{
    protected string $model = ExpenseCategory::class;

    protected string $viewNamespace = 'ExpenseCategories';

    protected string $permissionPrefix = 'expense_categories';

    protected string $routePrefix = 'admin.expense-categories';

    protected string $titleKey = 'coffee.expense_categories';

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
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
