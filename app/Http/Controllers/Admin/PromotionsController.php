<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionsController extends BaseCrudController
{
    protected string $model = Promotion::class;

    protected string $viewNamespace = 'Promotions';

    protected string $permissionPrefix = 'promotions';

    protected string $routePrefix = 'admin.promotions';

    protected string $titleKey = 'coffee.promotions';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'discount_type',
                'title' => 'coffee.type',
            ],
            2 => [
                'data' => 'discount_value',
                'title' => 'coffee.discount',
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
            'promo_code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['nullable', 'string'],
            'discount_value' => ['nullable', 'numeric'],
            'min_purchase_amount' => ['nullable', 'numeric'],
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
