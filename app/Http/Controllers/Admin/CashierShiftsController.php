<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use App\Models\PosCounter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class CashierShiftsController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('cashier_shifts.view')), 403);

        $companyId = $user->company_id;
        $branchId = session('active_branch_id');

        $activeShift = CashierShift::query()
            ->where('cashier_id', $user->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        return Inertia::render('CashierShifts/Index', [
            'titleKey' => 'coffee.cashier_shifts',
            'routes' => [
                'data' => 'admin.cashier-shifts.data',
                'open' => 'admin.cashier-shifts.open',
                'close' => 'admin.cashier-shifts.close',
            ],
            'columns' => [
                ['data' => 'shift_no', 'name' => 'shift_no', 'title' => __('coffee.code')],
                ['data' => 'opened_at', 'name' => 'opened_at', 'title' => __('coffee.from')],
                ['data' => 'closed_at', 'name' => 'closed_at', 'title' => __('coffee.to')],
                ['data' => 'opening_cash', 'name' => 'opening_cash', 'title' => __('coffee.opening_cash')],
                ['data' => 'closing_cash', 'name' => 'closing_cash', 'title' => __('coffee.closing_cash')],
                ['data' => 'total_sales', 'name' => 'total_sales', 'title' => __('coffee.total_sales')],
                ['data' => 'status', 'name' => 'status', 'title' => __('coffee.status')],
            ],
            'activeShift' => $activeShift,
            'counters' => PosCounter::query()
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->where('status', 'active')
                ->get(['id', 'name']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $q = CashierShift::query()
            ->when($companyId, fn ($q, $cid) => $q->where('company_id', $cid));

        return DataTables::of($q)
            ->editColumn('opened_at', fn ($s) => optional($s->opened_at ? Carbon::parse($s->opened_at) : null)?->format('Y-m-d H:i'))
            ->editColumn('closed_at', fn ($s) => optional($s->closed_at ? Carbon::parse($s->closed_at) : null)?->format('Y-m-d H:i'))
            ->editColumn('opening_cash', fn ($s) => number_format((float) $s->opening_cash, 2))
            ->editColumn('closing_cash', fn ($s) => number_format((float) $s->closing_cash, 2))
            ->editColumn('total_sales', fn ($s) => number_format((float) $s->total_sales, 2))
            ->toJson();
    }

    public function open(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('cashier_shifts.open')), 403);

        $branchId = session('active_branch_id');
        abort_unless($branchId, 422, 'No active branch.');

        $validated = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'pos_counter_id' => ['nullable', 'integer', 'exists:pos_counters,id'],
            'note' => ['nullable', 'string'],
        ]);

        if (CashierShift::where('cashier_id', $user->id)->where('status', 'open')->exists()) {
            return back()->with('error', __('coffee.shift_already_open'));
        }

        CashierShift::create([
            'company_id' => $user->company_id,
            'branch_id' => $branchId,
            'pos_counter_id' => $validated['pos_counter_id'] ?? null,
            'cashier_id' => $user->id,
            'shift_no' => 'SH-'.now()->format('YmdHis'),
            'opening_cash' => $validated['opening_cash'],
            'opened_at' => now(),
            'status' => 'open',
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('success', __('coffee.shift_opened'));
    }

    public function close(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('cashier_shifts.close')), 403);

        $shift = CashierShift::findOrFail($id);
        abort_unless($shift->cashier_id === $user->id || $user->isSuperAdmin(), 403);

        $validated = $request->validate([
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        $totalSales = (float) $shift->orders()->where('payment_status', 'paid')->sum('grand_total') ?: 0;

        $shift->update([
            'closing_cash' => $validated['closing_cash'],
            'system_cash' => $shift->opening_cash + $totalSales,
            'cash_difference' => $validated['closing_cash'] - ($shift->opening_cash + $totalSales),
            'total_sales' => $totalSales,
            'closed_at' => now(),
            'status' => 'closed',
            'note' => trim(($shift->note ?? '')."\n".($validated['note'] ?? '')),
        ]);

        return back()->with('success', __('coffee.shift_closed'));
    }
}
