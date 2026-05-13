<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponsController extends BaseCrudController
{
    protected string $model = Coupon::class;

    protected string $viewNamespace = 'Coupons';

    protected string $permissionPrefix = 'coupons';

    protected string $routePrefix = 'admin.coupons';

    protected string $titleKey = 'coffee.coupons';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'code',
                'title' => 'coffee.code',
            ],
            1 => [
                'data' => 'discount_value',
                'title' => 'coffee.discount',
            ],
            2 => [
                'data' => 'usage_count',
                'title' => 'coffee.quantity',
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
            'description' => ['nullable', 'string'],
            'discount_type' => ['nullable', 'string'],
            'discount_value' => ['nullable', 'numeric'],
            'min_purchase_amount' => ['nullable', 'numeric'],
            'usage_limit' => ['nullable', 'numeric'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
