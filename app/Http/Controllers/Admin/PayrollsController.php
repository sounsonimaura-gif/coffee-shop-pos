<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\Payroll;
use App\Models\Staff;
use Illuminate\Http\Request;

class PayrollsController extends BaseCrudController
{
    protected string $model = Payroll::class;

    protected string $viewNamespace = 'Payrolls';

    protected string $permissionPrefix = 'payrolls';

    protected string $routePrefix = 'admin.payrolls';

    protected string $titleKey = 'coffee.payrolls';

    protected function columns(): array
    {
        return [
            ['data' => 'payroll_no', 'title' => 'coffee.code'],
            ['data' => 'period_month', 'title' => 'coffee.period'],
            ['data' => 'staff_id', 'title' => 'coffee.staff'],
            ['data' => 'net_salary', 'title' => 'coffee.net_salary'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'payment_method_id' => ['nullable', 'integer', 'exists:payment_methods,id'],
            'payroll_no' => ['nullable', 'string', 'max:80'],
            'period_month' => ['required', 'string', 'max:20'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'commission_amount' => ['nullable', 'numeric', 'min:0'],
            'bonus_amount' => ['nullable', 'numeric', 'min:0'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'net_salary' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,approved,paid,cancelled'],
        ];
    }

    protected function beforeSave(array $payload, Request $request, ?object $model = null): array
    {
        $payload = parent::beforeSave($payload, $request, $model);

        $basic = (float) ($payload['basic_salary'] ?? 0);
        $comm = (float) ($payload['commission_amount'] ?? 0);
        $bonus = (float) ($payload['bonus_amount'] ?? 0);
        $ded = (float) ($payload['deduction_amount'] ?? 0);
        $payload['net_salary'] = $basic + $comm + $bonus - $ded;

        if (empty($payload['payroll_no'])) {
            $payload['payroll_no'] = 'PR-'.now()->format('YmdHis').'-'.random_int(100, 999);
        }

        return $payload;
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'staff' => Staff::query()->where('company_id', $companyId)->select('id', 'name', 'staff_code')->orderBy('name')->get(),
            'branches' => Branch::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'payment_methods' => PaymentMethod::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'statuses' => [
                ['value' => 'draft', 'text' => 'Draft'],
                ['value' => 'approved', 'text' => 'Approved'],
                ['value' => 'paid', 'text' => 'Paid'],
                ['value' => 'cancelled', 'text' => 'Cancelled'],
            ],
        ];
    }
}
