<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodsController extends BaseCrudController
{
    protected string $model = PaymentMethod::class;

    protected string $viewNamespace = 'PaymentMethods';

    protected string $permissionPrefix = 'payment_methods';

    protected string $routePrefix = 'admin.payment-methods';

    protected string $titleKey = 'coffee.payment_methods';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            2 => [
                'data' => 'type',
                'title' => 'coffee.type',
            ],
            3 => [
                'data' => 'is_active',
                'title' => 'coffee.is_active',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string'],
            'account_no' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
