<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends BaseCrudController
{
    protected string $model = Staff::class;

    protected string $viewNamespace = 'Staff';

    protected string $permissionPrefix = 'staff';

    protected string $routePrefix = 'admin.staff';

    protected string $titleKey = 'coffee.staff';

    protected function columns(): array
    {
        return [
            ['data' => 'staff_code', 'title' => 'coffee.code'],
            ['data' => 'name', 'title' => 'coffee.name'],
            ['data' => 'position', 'title' => 'coffee.position'],
            ['data' => 'phone', 'title' => 'coffee.phone'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'staff_code' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'position' => ['nullable', 'string', 'max:100'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:active,inactive,terminated'],
        ];
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'branches' => Branch::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'users' => User::query()->where('company_id', $companyId)->select('id', 'name', 'email')->orderBy('name')->get(),
            'statuses' => [
                ['value' => 'active', 'text' => 'Active'],
                ['value' => 'inactive', 'text' => 'Inactive'],
                ['value' => 'terminated', 'text' => 'Terminated'],
            ],
        ];
    }
}
