<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CompaniesController extends BaseCrudController
{
    protected string $model = Company::class;

    protected string $viewNamespace = 'Companies';

    protected string $permissionPrefix = 'companies';

    protected string $routePrefix = 'admin.companies';

    protected string $titleKey = 'coffee.companies';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'company_code',
                'title' => 'coffee.company_code',
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
                'data' => 'email',
                'title' => 'coffee.email',
            ],
            4 => [
                'data' => 'status',
                'title' => 'coffee.status',
            ],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'company_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'tax_no' => ['nullable', 'string', 'max:255'],
            'currency_code' => ['nullable', 'string', 'max:255'],
            'language_code' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }

    protected function baseQuery(): Builder
    {
        return Company::query();
    }
}
