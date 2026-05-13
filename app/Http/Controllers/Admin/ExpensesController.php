<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class ExpensesController extends BaseCrudController
{
    protected string $model = Expense::class;

    protected string $viewNamespace = 'Expenses';

    protected string $permissionPrefix = 'expenses';

    protected string $routePrefix = 'admin.expenses';

    protected string $titleKey = 'coffee.expenses';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'expense_no',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'expense_date',
                'title' => 'coffee.from',
            ],
            2 => [
                'data' => 'amount',
                'title' => 'coffee.price',
            ],
            3 => [
                'data' => 'status',
                'title' => 'coffee.status',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'expense_category_id' => ['nullable', 'string'],
            'payment_method_id' => ['nullable', 'string'],
            'branch_id' => ['nullable', 'string'],
            'expense_no' => ['required', 'string', 'max:255'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'expense_categories' => ExpenseCategory::query()->select('id', 'name')->orderBy('name')->get(),
            'payment_methods' => PaymentMethod::query()->select('id', 'name')->orderBy('name')->get(),
            'branches' => Branch::query()
                ->when(request()->user()?->company_id, fn ($q, $cid) => $q->where('company_id', $cid))
                ->orderBy('name')->get(['id', 'name']),
        ];
    }
}
