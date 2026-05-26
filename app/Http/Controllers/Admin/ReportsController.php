<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\SaleInvoice;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class ReportsController extends Controller
{
    public function sales(Request $request): Response
    {
        $this->ensure($request, 'reports.sales');

        return Inertia::render('Reports/Sales', [
            'titleKey' => 'coffee.sale_invoices',
            'routes' => ['data' => 'admin.reports.sales.data'],
            'columns' => [
                ['data' => 'sale_no', 'name' => 'sale_no', 'title' => 'coffee.code'],
                ['data' => 'sale_at', 'name' => 'sale_at', 'title' => 'coffee.from'],
                ['data' => 'grand_total', 'name' => 'grand_total', 'title' => 'coffee.total'],
                ['data' => 'paid_amount', 'name' => 'paid_amount', 'title' => 'coffee.paid'],
                ['data' => 'status', 'name' => 'status', 'title' => 'coffee.status'],
            ],
        ]);
    }

    public function salesData(Request $request): JsonResponse
    {
        $this->ensure($request, 'reports.sales');
        $cid = $request->user()->company_id;

        $q = SaleInvoice::query()->when($cid, fn ($q, $cid) => $q->where('company_id', $cid));

        return DataTables::of($q)
            ->editColumn('sale_at', fn ($s) => optional($s->sale_at ? Carbon::parse($s->sale_at) : null)?->format('Y-m-d H:i'))
            ->toJson();
    }

    public function inventory(Request $request): Response
    {
        $this->ensure($request, 'reports.inventory');

        return Inertia::render('Reports/Inventory', [
            'titleKey' => 'coffee.stock',
            'routes' => ['data' => 'admin.reports.inventory.data'],
            'columns' => [
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'coffee.from'],
                ['data' => 'movement_type', 'name' => 'movement_type', 'title' => 'coffee.type'],
                ['data' => 'ingredient_id', 'name' => 'ingredient_id', 'title' => 'coffee.ingredients'],
                ['data' => 'quantity', 'name' => 'quantity', 'title' => 'coffee.quantity'],
            ],
        ]);
    }

    public function inventoryData(Request $request): JsonResponse
    {
        $this->ensure($request, 'reports.inventory');

        return DataTables::of(StockMovement::query())->toJson();
    }

    public function expenses(Request $request): Response
    {
        $this->ensure($request, 'reports.expenses');

        return Inertia::render('Reports/Expenses', [
            'titleKey' => 'coffee.expenses',
            'routes' => ['data' => 'admin.reports.expenses.data'],
            'columns' => [
                ['data' => 'expense_no', 'name' => 'expense_no', 'title' => 'coffee.code'],
                ['data' => 'expense_date', 'name' => 'expense_date', 'title' => 'coffee.from'],
                ['data' => 'amount', 'name' => 'amount', 'title' => 'coffee.price'],
                ['data' => 'status', 'name' => 'status', 'title' => 'coffee.status'],
            ],
        ]);
    }

    public function expensesData(Request $request): JsonResponse
    {
        $this->ensure($request, 'reports.expenses');
        $cid = $request->user()->company_id;

        return DataTables::of(Expense::query()->when($cid, fn ($q, $cid) => $q->where('company_id', $cid)))
            ->toJson();
    }

    protected function ensure(Request $request, string $perm): void
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission($perm)), 403);
    }
}
