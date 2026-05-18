<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleInvoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class SaleInvoicesController extends Controller
{
    public function index(): Response
    {
        $this->authorize('view');

        return Inertia::render('SaleInvoices/Index', [
            'titleKey' => 'coffee.sale_invoices',
            'routes' => [
                'data' => 'admin.sale-invoices.data',
                'show' => 'admin.sale-invoices.show',
            ],
            'columns' => [
                ['data' => 'sale_no', 'name' => 'sale_no', 'title' => 'coffee.code'],
                ['data' => 'sale_at', 'name' => 'sale_at', 'title' => 'coffee.from'],
                ['data' => 'grand_total', 'name' => 'grand_total', 'title' => 'coffee.total'],
                ['data' => 'paid_amount', 'name' => 'paid_amount', 'title' => 'coffee.paid'],
                ['data' => 'status', 'name' => 'status', 'title' => 'coffee.status'],
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('view');
        $companyId = $request->user()?->company_id;
        $branchId = session('active_branch_id');

        $q = SaleInvoice::query()
            ->when($companyId, fn ($q, $cid) => $q->where('sale_invoices.company_id', $cid))
            ->when($branchId, fn ($q, $bid) => $q->where('sale_invoices.branch_id', $bid));

        $dt = DataTables::of($q);
        $dt->editColumn('sale_at', fn ($s) => optional($s->sale_at ? Carbon::parse($s->sale_at) : null)?->format('Y-m-d H:i'));
        $dt->editColumn('grand_total', fn ($s) => number_format((float) $s->grand_total, 2));
        $dt->editColumn('paid_amount', fn ($s) => number_format((float) $s->paid_amount, 2));

        return $dt->make(true);
    }

    public function show(int $id): Response
    {
        $this->authorize('view');
        $invoice = SaleInvoice::findOrFail($id);

        return Inertia::render('SaleInvoices/Show', ['invoice' => $invoice]);
    }

    protected function authorize(string $action): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission("sale_invoices.$action")), 403);
    }
}
