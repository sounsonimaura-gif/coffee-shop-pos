<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Customer;
use App\Models\MembershipLevel;
use Illuminate\Http\Request;

class CustomersController extends BaseCrudController
{
    protected string $model = Customer::class;

    protected string $viewNamespace = 'Customers';

    protected string $permissionPrefix = 'customers';

    protected string $routePrefix = 'admin.customers';

    protected string $titleKey = 'coffee.customers';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'customer_code',
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
            'membership_level_id' => ['nullable', 'string'],
            'customer_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'birthday' => ['nullable', 'date'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'membership_levels' => MembershipLevel::query()->select('id', 'name')->orderBy('name')->get(),
        ];
    }
}
