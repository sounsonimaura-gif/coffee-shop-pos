<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use App\Models\Order;
use App\Models\SaleInvoice;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = $request->user()?->company_id;
        $branchId = session('active_branch_id');

        $today = now()->startOfDay();

        $stats = [
            'sales_today' => number_format((float) SaleInvoice::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->whereDate('sale_at', $today)
                ->sum('grand_total'), 2),
            'orders_today' => (int) Order::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->whereDate('created_at', $today)
                ->count(),
            'open_shifts' => (int) CashierShift::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->where('status', 'open')
                ->count(),
            'low_stock_items' => (int) StockAlert::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->where('is_resolved', false)
                ->count(),
        ];

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
        ]);
    }
}
