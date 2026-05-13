<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Commission;
use App\Models\SaleInvoice;
use App\Models\Staff;
use Illuminate\Http\Request;

class CommissionsController extends BaseCrudController
{
    protected string $model = Commission::class;

    protected string $viewNamespace = 'Commissions';

    protected string $permissionPrefix = 'commissions';

    protected string $routePrefix = 'admin.commissions';

    protected string $titleKey = 'coffee.commissions';

    protected function columns(): array
    {
        return [
            ['data' => 'commission_date', 'title' => 'coffee.date'],
            ['data' => 'staff_id', 'title' => 'coffee.staff'],
            ['data' => 'commission_type', 'title' => 'coffee.type'],
            ['data' => 'base_amount', 'title' => 'coffee.base_amount'],
            ['data' => 'rate', 'title' => 'coffee.rate'],
            ['data' => 'commission_amount', 'title' => 'coffee.commission_amount'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'sale_invoice_id' => ['nullable', 'integer', 'exists:sale_invoices,id'],
            'commission_type' => ['required', 'in:sale_percent,sale_fixed,menu_percent,menu_fixed,branch_percent'],
            'base_amount' => ['required', 'numeric', 'min:0'],
            'rate' => ['required', 'numeric', 'min:0'],
            'commission_amount' => ['nullable', 'numeric', 'min:0'],
            'commission_date' => ['required', 'date'],
            'status' => ['nullable', 'in:pending,approved,paid,cancelled'],
        ];
    }

    protected function beforeSave(array $payload, Request $request, ?object $model = null): array
    {
        $payload = parent::beforeSave($payload, $request, $model);

        $type = $payload['commission_type'] ?? 'sale_percent';
        $base = (float) ($payload['base_amount'] ?? 0);
        $rate = (float) ($payload['rate'] ?? 0);
        $payload['commission_amount'] = str_contains($type, 'percent') ? ($base * $rate / 100) : $rate;

        return $payload;
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'staff' => Staff::query()->where('company_id', $companyId)->select('id', 'name', 'staff_code')->orderBy('name')->get(),
            'branches' => Branch::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'sale_invoices' => SaleInvoice::query()->where('company_id', $companyId)->latest('id')->limit(200)->select('id', 'invoice_no', 'grand_total')->get(),
            'commission_types' => [
                ['value' => 'sale_percent', 'text' => 'Sale (%)'],
                ['value' => 'sale_fixed', 'text' => 'Sale (Fixed)'],
                ['value' => 'menu_percent', 'text' => 'Menu (%)'],
                ['value' => 'menu_fixed', 'text' => 'Menu (Fixed)'],
                ['value' => 'branch_percent', 'text' => 'Branch (%)'],
            ],
            'statuses' => [
                ['value' => 'pending', 'text' => 'Pending'],
                ['value' => 'approved', 'text' => 'Approved'],
                ['value' => 'paid', 'text' => 'Paid'],
                ['value' => 'cancelled', 'text' => 'Cancelled'],
            ],
        ];
    }
}
