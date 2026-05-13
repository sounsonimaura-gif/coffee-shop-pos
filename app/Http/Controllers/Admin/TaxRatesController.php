<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRatesController extends BaseCrudController
{
    protected string $model = TaxRate::class;

    protected string $viewNamespace = 'TaxRates';

    protected string $permissionPrefix = 'tax_rates';

    protected string $routePrefix = 'admin.tax-rates';

    protected string $titleKey = 'coffee.tax_rates';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'name',
                'title' => 'coffee.name',
            ],
            1 => [
                'data' => 'rate',
                'title' => 'coffee.discount',
            ],
            2 => [
                'data' => 'is_default',
                'title' => 'coffee.is_active',
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
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['nullable', 'numeric'],
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
