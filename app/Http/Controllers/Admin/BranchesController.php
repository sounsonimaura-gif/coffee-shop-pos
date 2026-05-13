<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchesController extends BaseCrudController
{
    protected string $model = Branch::class;

    protected string $viewNamespace = 'Branches';

    protected string $permissionPrefix = 'branches';

    protected string $routePrefix = 'admin.branches';

    protected string $titleKey = 'coffee.branches';

    protected function columns(): array
    {
        return [
            0 => [
                'data' => 'branch_code',
                'title' => 'coffee.branch_code',
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
            'branch_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'open_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'close_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'is_main_branch' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        return [
        ];
    }
}
