<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SuppliersController extends BaseCrudController
{
    protected string $model = Supplier::class;

    protected string $viewNamespace = 'Suppliers';

    protected string $permissionPrefix = 'suppliers';

    protected string $routePrefix = 'admin.suppliers';

    protected string $titleKey = 'coffee.suppliers';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'supplier_code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'phone',
                'title' => 'coffee.phone',
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
            'supplier_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'tax_no' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'credit_limit' => ['nullable', 'numeric'],
            'credit_days' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
