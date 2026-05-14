<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockBatch;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class PurchasesController extends Controller
{
    public function __construct(protected StockService $stock) {}

    public function index(): Response
    {
        $this->ensure('purchases.view');

        return Inertia::render('Purchases/Index', [
            'titleKey' => 'coffee.purchases',
            'routes' => [
                'data' => 'admin.purchases.data',
                'create' => 'admin.purchases.create',
                'edit' => 'admin.purchases.edit',
                'show' => 'admin.purchases.show',
                'destroy' => 'admin.purchases.destroy',
            ],
            'columns' => [
                ['data' => 'purchase_no', 'name' => 'purchase_no', 'title' => __('coffee.code')],
                ['data' => 'purchase_date', 'name' => 'purchase_date', 'title' => __('coffee.date')],
                ['data' => 'supplier_name', 'name' => 'supplier.name', 'title' => __('coffee.supplier')],
                ['data' => 'grand_total', 'name' => 'grand_total', 'title' => __('coffee.grand_total')],
                ['data' => 'purchase_status', 'name' => 'purchase_status', 'title' => __('coffee.status')],
                ['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('coffee.payment_status')],
            ],
            'permissions' => $this->permsForFront('purchases'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensure('purchases.view');

        $query = Purchase::query()
            ->with('supplier:id,name')
            ->where('company_id', $request->user()->company_id);

        return DataTables::of($query)
            ->addColumn('supplier_name', fn ($p) => $p->supplier?->name ?? '—')
            ->addColumn('actions', function ($p) {
                return view('admin.partials.row_actions', [
                    'id' => $p->id,
                    'showRoute' => 'admin.purchases.show',
                    'editRoute' => 'admin.purchases.edit',
                    'destroyRoute' => 'admin.purchases.destroy',
                    'canEdit' => $this->canPerm('purchases.update'),
                    'canDelete' => $this->canPerm('purchases.delete'),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): Response
    {
        $this->ensure('purchases.create');

        return Inertia::render('Purchases/Form', [
            'titleKey' => 'coffee.purchases',
            'isEdit' => false,
            'model' => (object) ['items' => []],
            'props' => $this->formProps(),
            'routes' => [
                'store' => 'admin.purchases.store',
                'index' => 'admin.purchases.index',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensure('purchases.create');

        $payload = $this->validatePurchase($request);

        DB::transaction(function () use ($payload, $request) {
            $purchase = Purchase::create([
                'company_id' => $request->user()->company_id,
                'branch_id' => $payload['branch_id'] ?? session('active_branch_id'),
                'warehouse_id' => $payload['warehouse_id'] ?? null,
                'supplier_id' => $payload['supplier_id'] ?? null,
                'created_by' => $request->user()->id,
                'purchase_no' => 'PO-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'purchase_date' => $payload['purchase_date'] ?? now()->toDateString(),
                'due_date' => $payload['due_date'] ?? null,
                'note' => $payload['note'] ?? null,
                'purchase_status' => $payload['purchase_status'] ?? 'received',
                'payment_status' => 'unpaid',
                'subtotal' => 0,
                'discount_amount' => $payload['discount_amount'] ?? 0,
                'tax_amount' => $payload['tax_amount'] ?? 0,
                'shipping_amount' => $payload['shipping_amount'] ?? 0,
                'grand_total' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
            ]);

            $subtotal = 0;
            foreach ($payload['items'] as $row) {
                $lineTotal = ((float) $row['quantity']) * ((float) $row['unit_cost']);
                $item = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'ingredient_id' => $row['ingredient_id'],
                    'unit_id' => $row['unit_id'] ?? null,
                    'batch_no' => $row['batch_no'] ?? null,
                    'expiry_date' => $row['expiry_date'] ?? null,
                    'quantity' => $row['quantity'],
                    'received_quantity' => $row['quantity'],
                    'unit_cost' => $row['unit_cost'],
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'line_total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;

                if ($purchase->purchase_status === 'received' || $purchase->purchase_status === 'partial_received') {
                    $batch = null;
                    if (! empty($row['batch_no'])) {
                        $batch = StockBatch::create([
                            'company_id' => $purchase->company_id,
                            'branch_id' => $purchase->branch_id,
                            'warehouse_id' => $purchase->warehouse_id,
                            'ingredient_id' => $row['ingredient_id'],
                            'supplier_id' => $purchase->supplier_id,
                            'purchase_item_id' => $item->id,
                            'batch_no' => $row['batch_no'],
                            'expiry_date' => $row['expiry_date'] ?? null,
                            'initial_quantity' => $row['quantity'],
                            'current_quantity' => $row['quantity'],
                            'unit_cost' => $row['unit_cost'],
                            'status' => 'active',
                        ]);
                    }

                    $this->stock->record([
                        'company_id' => $purchase->company_id,
                        'branch_id' => $purchase->branch_id,
                        'warehouse_id' => $purchase->warehouse_id,
                        'ingredient_id' => $row['ingredient_id'],
                        'stock_batch_id' => $batch?->id,
                        'created_by' => $request->user()->id,
                        'movement_type' => 'purchase_in',
                        'reference_type' => Purchase::class,
                        'reference_id' => $purchase->id,
                        'reference_no' => $purchase->purchase_no,
                        'quantity_in' => $row['quantity'],
                        'quantity_out' => 0,
                        'unit_cost' => $row['unit_cost'],
                    ]);
                }
            }

            $grand = $subtotal - (float) $purchase->discount_amount + (float) $purchase->tax_amount + (float) $purchase->shipping_amount;
            $purchase->update([
                'subtotal' => $subtotal,
                'grand_total' => $grand,
                'due_amount' => $grand,
            ]);
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', __('coffee.saved_successfully'));
    }

    public function show(int $id): Response
    {
        $this->ensure('purchases.view');

        $purchase = Purchase::query()
            ->with(['items.ingredient:id,name', 'items.unit:id,name', 'supplier', 'branch', 'warehouse'])
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);

        return Inertia::render('Purchases/Show', [
            'purchase' => $purchase,
        ]);
    }

    public function edit(int $id): Response
    {
        $this->ensure('purchases.update');

        $purchase = Purchase::query()
            ->with('items')
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);

        $model = $purchase->toArray();
        $model['items'] = $purchase->items->map(fn ($i) => $i->toArray())->all();

        return Inertia::render('Purchases/Form', [
            'titleKey' => 'coffee.purchases',
            'isEdit' => true,
            'model' => $model,
            'props' => $this->formProps(),
            'routes' => [
                'update' => ['admin.purchases.update', $id],
                'index' => 'admin.purchases.index',
            ],
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->ensure('purchases.update');

        $purchase = Purchase::query()
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $payload = $this->validatePurchase($request);

        DB::transaction(function () use ($purchase, $payload) {
            $purchase->update([
                'branch_id' => $payload['branch_id'] ?? $purchase->branch_id,
                'warehouse_id' => $payload['warehouse_id'] ?? $purchase->warehouse_id,
                'supplier_id' => $payload['supplier_id'] ?? null,
                'purchase_date' => $payload['purchase_date'] ?? $purchase->purchase_date,
                'due_date' => $payload['due_date'] ?? null,
                'note' => $payload['note'] ?? null,
                'purchase_status' => $payload['purchase_status'] ?? $purchase->purchase_status,
                'discount_amount' => $payload['discount_amount'] ?? 0,
                'tax_amount' => $payload['tax_amount'] ?? 0,
                'shipping_amount' => $payload['shipping_amount'] ?? 0,
            ]);

            // Replace line items (note: any stock movements are not rolled
            // back here — for production you'd reverse the original
            // movement and re-write. For simplicity, edits are only safe on
            // draft purchases.)
            $purchase->items()->delete();

            $subtotal = 0;
            foreach ($payload['items'] as $row) {
                $lineTotal = ((float) $row['quantity']) * ((float) $row['unit_cost']);
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'ingredient_id' => $row['ingredient_id'],
                    'unit_id' => $row['unit_id'] ?? null,
                    'batch_no' => $row['batch_no'] ?? null,
                    'expiry_date' => $row['expiry_date'] ?? null,
                    'quantity' => $row['quantity'],
                    'received_quantity' => $row['quantity'],
                    'unit_cost' => $row['unit_cost'],
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'line_total' => $lineTotal,
                ]);
                $subtotal += $lineTotal;
            }

            $grand = $subtotal - (float) $purchase->discount_amount + (float) $purchase->tax_amount + (float) $purchase->shipping_amount;
            $purchase->update([
                'subtotal' => $subtotal,
                'grand_total' => $grand,
                'due_amount' => $grand - (float) $purchase->paid_amount,
            ]);
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->ensure('purchases.delete');

        $purchase = Purchase::query()
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);
        $purchase->update(['purchase_status' => 'cancelled']);
        $purchase->delete();

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function validatePurchase(Request $request): array
    {
        return $request->validate([
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'purchase_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'purchase_status' => ['nullable', 'in:draft,ordered,received,partial_received,cancelled'],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'items.*.unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'items.*.batch_no' => ['nullable', 'string', 'max:255'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);
    }

    protected function formProps(): array
    {
        return [
            'suppliers' => Supplier::query()->select('id', 'name')->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->select('id', 'name')->orderBy('name')->get(),
            'ingredients' => Ingredient::query()->select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::query()->select('id', 'name', 'symbol')->orderBy('name')->get(),
            'purchase_statuses' => [
                ['value' => 'draft', 'text' => 'Draft'],
                ['value' => 'ordered', 'text' => 'Ordered'],
                ['value' => 'received', 'text' => 'Received'],
                ['value' => 'partial_received', 'text' => 'Partially Received'],
                ['value' => 'cancelled', 'text' => 'Cancelled'],
            ],
        ];
    }

    protected function ensure(string $perm): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission($perm)), 403);
    }

    protected function canPerm(string $perm): bool
    {
        $user = request()->user();
        if (! $user) {
            return false;
        }
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermission($perm);
    }

    protected function permsForFront(string $module): array
    {
        return [
            'view' => $this->canPerm("$module.view"),
            'create' => $this->canPerm("$module.create"),
            'update' => $this->canPerm("$module.update"),
            'delete' => $this->canPerm("$module.delete"),
        ];
    }
}
