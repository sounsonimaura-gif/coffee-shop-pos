<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\SaleInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class OrdersController extends Controller
{
    public function index(): Response
    {
        $this->authorize('view');

        return Inertia::render('Orders/Index', [
            'titleKey' => 'coffee.orders',
            'routes' => [
                'data' => 'admin.orders.data',
                'show' => 'admin.orders.show',
                'destroy' => 'admin.orders.destroy',
                'create' => 'admin.pos.index', // create via POS
            ],
            'columns' => [
                ['data' => 'order_no', 'title' => __('coffee.code'), 'name' => 'order_no'],
                ['data' => 'order_type', 'title' => __('coffee.type'), 'name' => 'order_type'],
                ['data' => 'grand_total', 'title' => __('coffee.total'), 'name' => 'grand_total'],
                ['data' => 'payment_status', 'title' => __('coffee.status'), 'name' => 'payment_status'],
                ['data' => 'status', 'title' => __('coffee.status'), 'name' => 'status'],
                ['data' => 'created_at', 'title' => __('coffee.from'), 'name' => 'created_at'],
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('view');

        $companyId = $request->user()?->company_id;
        $branchId = session('active_branch_id');

        $q = Order::query()
            ->when($companyId, fn ($q, $cid) => $q->where('orders.company_id', $cid))
            ->when($branchId, fn ($q, $bid) => $q->where('orders.branch_id', $bid));

        $dt = DataTables::of($q);
        $dt->editColumn('grand_total', fn ($o) => number_format((float) $o->grand_total, 2));
        $dt->editColumn('created_at', fn ($o) => optional($o->created_at)->format('Y-m-d H:i'));
        $dt->addColumn('actions', fn ($o) => view('admin.partials.row_actions', [
            'id' => $o->id,
            'editRoute' => 'admin.orders.show',
            'destroyRoute' => 'admin.orders.destroy',
            'canEdit' => true,
            'canDelete' => $request->user()?->hasAnyPermission(['orders.delete', 'orders.void']) || $request->user()?->isSuperAdmin(),
        ])->render());
        $dt->rawColumns(['actions']);

        return $dt->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('orders.create')), 403);

        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'table_id' => ['nullable', 'integer', 'exists:dining_tables,id'],
            'order_type' => ['required', 'in:dine_in,takeaway,delivery,online_order,staff_meal,complimentary'],
            'note' => ['nullable', 'string'],
            'discount_amount' => ['nullable', 'numeric'],
            'tax_amount' => ['nullable', 'numeric'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.note' => ['nullable', 'string'],
            'payments' => ['nullable', 'array'],
            'payments.*.payment_method_id' => ['required_with:payments', 'integer', 'exists:payment_methods,id'],
            'payments.*.amount' => ['required_with:payments', 'numeric', 'min:0'],
            'payments.*.reference_no' => ['nullable', 'string'],
        ]);

        $branchId = session('active_branch_id');
        abort_unless($branchId, 422, 'No active branch.');

        $shift = CashierShift::query()
            ->where('cashier_id', $user->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        $order = DB::transaction(function () use ($validated, $user, $branchId, $shift) {
            $orderNo = 'ORD-'.now()->format('YmdHis').'-'.str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

            $subtotal = collect($validated['items'])->sum(fn ($i) => $i['quantity'] * $i['unit_price']);
            $discount = (float) ($validated['discount_amount'] ?? 0);
            $tax = (float) ($validated['tax_amount'] ?? 0);
            $grand = max(0, $subtotal - $discount + $tax);

            $order = Order::create([
                'company_id' => $user->company_id,
                'branch_id' => $branchId,
                'customer_id' => $validated['customer_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'cashier_shift_id' => $shift?->id,
                'created_by' => $user->id,
                'order_no' => $orderNo,
                'order_type' => $validated['order_type'],
                'source' => 'pos',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'grand_total' => $grand,
                'paid_amount' => 0,
                'change_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'note' => $validated['note'] ?? null,
                'ordered_at' => now(),
            ]);

            foreach ($validated['items'] as $line) {
                $menu = MenuItem::find($line['menu_item_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $line['menu_item_id'],
                    'item_name' => $menu?->name ?? '',
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['quantity'] * $line['unit_price'],
                    'special_note' => $line['note'] ?? null,
                    'kitchen_status' => 'pending',
                ]);
            }

            // Create sale invoice + payments if payments provided
            if (! empty($validated['payments'])) {
                $paid = collect($validated['payments'])->sum('amount');
                $change = max(0, $paid - $grand);

                $invoice = SaleInvoice::create([
                    'company_id' => $user->company_id,
                    'branch_id' => $branchId,
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'cashier_shift_id' => $shift?->id,
                    'cashier_id' => $user->id,
                    'sale_no' => 'INV-'.now()->format('YmdHis').'-'.str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT),
                    'sale_at' => now(),
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'tax_amount' => $tax,
                    'grand_total' => $grand,
                    'paid_amount' => $paid,
                    'change_amount' => $change,
                    'status' => 'posted',
                ]);

                foreach ($validated['payments'] as $p) {
                    Payment::create([
                        'company_id' => $user->company_id,
                        'branch_id' => $branchId,
                        'sale_invoice_id' => $invoice->id,
                        'payment_method_id' => $p['payment_method_id'],
                        'amount' => $p['amount'],
                        'reference_no' => $p['reference_no'] ?? null,
                        'paid_at' => now(),
                        'status' => 'completed',
                    ]);
                }

                $order->update([
                    'paid_amount' => $paid,
                    'change_amount' => $change,
                    'payment_status' => $paid >= $grand ? 'paid' : 'partial_paid',
                    'status' => 'completed',
                ]);
            }

            return $order;
        });

        return back()->with('success', __('coffee.saved_successfully').' #'.$order->order_no);
    }

    public function show(int $id): Response
    {
        $this->authorize('view');
        $order = Order::with(['items.menuItem', 'customer', 'table'])->findOrFail($id);

        return Inertia::render('Orders/Show', ['order' => $order]);
    }

    public function destroy(int $id, Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasAnyPermission(['orders.delete', 'orders.void'])), 403);
        $order = Order::findOrFail($id);
        $order->update(['status' => 'voided']);

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function authorize(string $action): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission("orders.$action")), 403);
    }
}
