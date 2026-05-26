<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\LoyaltyPointTransaction;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;

class LoyaltyPointTransactionsController extends BaseReadOnlyController
{
    protected string $model = LoyaltyPointTransaction::class;

    protected string $viewNamespace = 'LoyaltyPointTransactions';

    protected string $permissionPrefix = 'loyalty_point_transactions';

    protected string $routePrefix = 'admin.loyalty-point-transactions';

    protected string $titleKey = 'coffee.loyalty_point_transactions';

    protected function columns(): array
    {
        return [
            ['data' => 'created_at', 'title' => 'coffee.date'],
            ['data' => 'customer_name', 'name' => 'customer.name', 'title' => 'coffee.customer'],
            ['data' => 'transaction_type', 'title' => 'coffee.type'],
            ['data' => 'points', 'title' => 'coffee.points'],
            ['data' => 'balance_after', 'title' => 'coffee.total'],
            ['data' => 'amount_value', 'title' => 'coffee.amount'],
            ['data' => 'note', 'title' => 'coffee.note'],
        ];
    }

    protected function baseQuery(): Builder
    {
        $q = LoyaltyPointTransaction::query()->with('customer');
        $companyId = request()->user()?->company_id;
        if ($companyId) {
            $q->where('company_id', $companyId);
        }

        return $q;
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt
            ->addColumn('customer_name', fn ($t) => $t->customer?->name ?? '-')
            ->editColumn('created_at', fn ($t) => optional($t->created_at)?->format('Y-m-d H:i'));
    }
}
