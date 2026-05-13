<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\WasteRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

/**
 * Shared list-only controller for stock-related sub-tables that are
 * primarily auto-written by other flows (purchases, stock transfers,
 * stock adjustments, waste records). Provides a DataTable list with
 * search/filter; full write flows can be added in a follow-up pass.
 */
class GenericListController extends Controller
{
    public function purchasesIndex(): Response
    {
        return $this->render('Purchases/Index', 'purchases', [
            ['data' => 'purchase_no', 'name' => 'purchase_no', 'title' => __('coffee.code')],
            ['data' => 'purchase_date', 'name' => 'purchase_date', 'title' => __('coffee.from')],
            ['data' => 'total_amount', 'name' => 'total_amount', 'title' => __('coffee.price')],
            ['data' => 'status', 'name' => 'status', 'title' => __('coffee.status')],
        ], 'coffee.purchases', 'admin.purchases.data');
    }

    public function purchasesData(Request $request)
    {
        $this->ensure('purchases.view');

        return DataTables::of(Purchase::query()->where('company_id', $request->user()->company_id))->toJson();
    }

    public function stockTransfersIndex(): Response
    {
        return $this->render('StockTransfers/Index', 'stock_transfers', [
            ['data' => 'transfer_no', 'name' => 'transfer_no', 'title' => __('coffee.code')],
            ['data' => 'transfer_date', 'name' => 'transfer_date', 'title' => __('coffee.from')],
            ['data' => 'status', 'name' => 'status', 'title' => __('coffee.status')],
        ], 'coffee.stock_transfers', 'admin.stock-transfers.data');
    }

    public function stockTransfersData(Request $request)
    {
        $this->ensure('stock_transfers.view');

        return DataTables::of(StockTransfer::query()->where('company_id', $request->user()->company_id))->toJson();
    }

    public function stockAdjustmentsIndex(): Response
    {
        return $this->render('StockAdjustments/Index', 'stock_adjustments', [
            ['data' => 'adjustment_no', 'name' => 'adjustment_no', 'title' => __('coffee.code')],
            ['data' => 'adjustment_date', 'name' => 'adjustment_date', 'title' => __('coffee.from')],
            ['data' => 'status', 'name' => 'status', 'title' => __('coffee.status')],
        ], 'coffee.stock_adjustments', 'admin.stock-adjustments.data');
    }

    public function stockAdjustmentsData(Request $request)
    {
        $this->ensure('stock_adjustments.view');

        return DataTables::of(StockAdjustment::query()->where('company_id', $request->user()->company_id))->toJson();
    }

    public function wasteRecordsIndex(): Response
    {
        return $this->render('WasteRecords/Index', 'waste_records', [
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('coffee.from')],
            ['data' => 'quantity', 'name' => 'quantity', 'title' => __('coffee.quantity')],
            ['data' => 'reason', 'name' => 'reason', 'title' => __('coffee.description')],
        ], 'coffee.waste_records', 'admin.waste-records.data');
    }

    public function wasteRecordsData(Request $request)
    {
        $this->ensure('waste_records.view');

        return DataTables::of(WasteRecord::query()->where('company_id', $request->user()->company_id))->toJson();
    }

    protected function render(string $page, string $perm, array $columns, string $titleKey, string $dataRoute): Response
    {
        $this->ensure($perm.'.view');

        return Inertia::render($page, [
            'titleKey' => $titleKey,
            'routes' => ['data' => $dataRoute],
            'columns' => $columns,
        ]);
    }

    protected function ensure(string $perm): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission($perm)), 403);
    }
}
