<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
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

class StockTransfersController extends Controller
{
    public function __construct(protected StockService $stock) {}

    public function index(): Response
    {
        $this->ensure('stock_transfers.view');

        return Inertia::render('StockTransfers/Index', [
            'titleKey' => 'coffee.stock_transfers',
            'routes' => [
                'data' => 'admin.stock-transfers.data',
                'create' => 'admin.stock-transfers.create',
                'edit' => 'admin.stock-transfers.edit',
                'show' => 'admin.stock-transfers.show',
                'destroy' => 'admin.stock-transfers.destroy',
            ],
            'columns' => [
                ['data' => 'transfer_no', 'name' => 'transfer_no', 'title' => 'coffee.code'],
                ['data' => 'transfer_date', 'name' => 'transfer_date', 'title' => 'coffee.date'],
                ['data' => 'from_branch_name', 'name' => 'fromBranch.name', 'title' => 'coffee.from_branch'],
                ['data' => 'to_branch_name', 'name' => 'toBranch.name', 'title' => 'coffee.to_branch'],
                ['data' => 'status', 'name' => 'status', 'title' => 'coffee.status'],
            ],
            'permissions' => $this->permsForFront('stock_transfers'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensure('stock_transfers.view');

        $query = StockTransfer::query()
            ->with(['fromBranch:id,name', 'toBranch:id,name'])
            ->where('company_id', $request->user()->company_id);

        return DataTables::of($query)
            ->addColumn('from_branch_name', fn ($p) => $p->fromBranch?->name ?? '—')
            ->addColumn('to_branch_name', fn ($p) => $p->toBranch?->name ?? '—')
            ->addColumn('actions', function ($p) {
                return view('admin.partials.row_actions', [
                    'id' => $p->id,
                    'showRoute' => 'admin.stock-transfers.show',
                    'editRoute' => 'admin.stock-transfers.edit',
                    'destroyRoute' => 'admin.stock-transfers.destroy',
                    'canEdit' => $this->canPerm('stock_transfers.update'),
                    'canDelete' => $this->canPerm('stock_transfers.delete'),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): Response
    {
        $this->ensure('stock_transfers.create');

        return Inertia::render('StockTransfers/Form', [
            'titleKey' => 'coffee.stock_transfers',
            'isEdit' => false,
            'model' => (object) ['items' => []],
            'props' => $this->formProps(),
            'routes' => [
                'store' => 'admin.stock-transfers.store',
                'index' => 'admin.stock-transfers.index',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensure('stock_transfers.create');

        $payload = $this->validateTransfer($request);

        DB::transaction(function () use ($payload, $request) {
            $transfer = StockTransfer::create([
                'company_id' => $request->user()->company_id,
                'from_branch_id' => $payload['from_branch_id'] ?? null,
                'to_branch_id' => $payload['to_branch_id'] ?? null,
                'from_warehouse_id' => $payload['from_warehouse_id'] ?? null,
                'to_warehouse_id' => $payload['to_warehouse_id'] ?? null,
                'created_by' => $request->user()->id,
                'transfer_no' => 'TR-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'transfer_date' => $payload['transfer_date'] ?? now()->toDateString(),
                'status' => $payload['status'] ?? 'received',
                'note' => $payload['note'] ?? null,
            ]);

            foreach ($payload['items'] as $row) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'ingredient_id' => $row['ingredient_id'],
                    'unit_id' => $row['unit_id'] ?? null,
                    'quantity_requested' => $row['quantity'],
                    'quantity_sent' => $row['quantity'],
                    'quantity_received' => $row['quantity'],
                ]);

                if ($transfer->status === 'received') {
                    // Out from source
                    $this->stock->record([
                        'company_id' => $transfer->company_id,
                        'branch_id' => $transfer->from_branch_id,
                        'warehouse_id' => $transfer->from_warehouse_id,
                        'ingredient_id' => $row['ingredient_id'],
                        'created_by' => $request->user()->id,
                        'movement_type' => 'transfer_out',
                        'reference_type' => StockTransfer::class,
                        'reference_id' => $transfer->id,
                        'reference_no' => $transfer->transfer_no,
                        'quantity_in' => 0,
                        'quantity_out' => $row['quantity'],
                    ]);
                    // In to destination
                    $this->stock->record([
                        'company_id' => $transfer->company_id,
                        'branch_id' => $transfer->to_branch_id,
                        'warehouse_id' => $transfer->to_warehouse_id,
                        'ingredient_id' => $row['ingredient_id'],
                        'created_by' => $request->user()->id,
                        'movement_type' => 'transfer_in',
                        'reference_type' => StockTransfer::class,
                        'reference_id' => $transfer->id,
                        'reference_no' => $transfer->transfer_no,
                        'quantity_in' => $row['quantity'],
                        'quantity_out' => 0,
                    ]);
                }
            }
        });

        return redirect()->route('admin.stock-transfers.index')
            ->with('success', __('coffee.saved_successfully'));
    }

    public function show(int $id): Response
    {
        $this->ensure('stock_transfers.view');

        $transfer = StockTransfer::query()
            ->with(['items.ingredient:id,name', 'items.unit:id,name', 'fromBranch', 'toBranch'])
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);

        return Inertia::render('StockTransfers/Show', [
            'transfer' => $transfer,
        ]);
    }

    public function edit(int $id): Response
    {
        $this->ensure('stock_transfers.update');

        $transfer = StockTransfer::query()
            ->with('items')
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);

        $model = $transfer->toArray();
        $model['items'] = $transfer->items->map(fn ($i) => [
            'ingredient_id' => $i->ingredient_id,
            'unit_id' => $i->unit_id,
            'quantity' => $i->quantity_requested,
        ])->all();

        return Inertia::render('StockTransfers/Form', [
            'titleKey' => 'coffee.stock_transfers',
            'isEdit' => true,
            'model' => $model,
            'props' => $this->formProps(),
            'routes' => [
                'update' => ['admin.stock-transfers.update', $id],
                'index' => 'admin.stock-transfers.index',
            ],
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->ensure('stock_transfers.update');

        $transfer = StockTransfer::query()
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $payload = $this->validateTransfer($request);

        DB::transaction(function () use ($transfer, $payload) {
            $transfer->update([
                'from_branch_id' => $payload['from_branch_id'] ?? null,
                'to_branch_id' => $payload['to_branch_id'] ?? null,
                'from_warehouse_id' => $payload['from_warehouse_id'] ?? null,
                'to_warehouse_id' => $payload['to_warehouse_id'] ?? null,
                'transfer_date' => $payload['transfer_date'] ?? $transfer->transfer_date,
                'status' => $payload['status'] ?? $transfer->status,
                'note' => $payload['note'] ?? null,
            ]);

            $transfer->items()->delete();

            foreach ($payload['items'] as $row) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'ingredient_id' => $row['ingredient_id'],
                    'unit_id' => $row['unit_id'] ?? null,
                    'quantity_requested' => $row['quantity'],
                    'quantity_sent' => $row['quantity'],
                    'quantity_received' => $row['quantity'],
                ]);
            }
        });

        return redirect()->route('admin.stock-transfers.index')
            ->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->ensure('stock_transfers.delete');

        $transfer = StockTransfer::query()
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);
        $transfer->update(['status' => 'cancelled']);
        $transfer->delete();

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function validateTransfer(Request $request): array
    {
        return $request->validate([
            'from_branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'to_branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'from_warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'to_warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'transfer_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,requested,approved,sent,received,cancelled'],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'items.*.unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ]);
    }

    protected function formProps(): array
    {
        return [
            'branches' => Branch::query()->select('id', 'name')->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->select('id', 'name')->orderBy('name')->get(),
            'ingredients' => Ingredient::query()->select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::query()->select('id', 'name', 'symbol')->orderBy('name')->get(),
            'transfer_statuses' => [
                ['value' => 'draft', 'text' => 'Draft'],
                ['value' => 'requested', 'text' => 'Requested'],
                ['value' => 'approved', 'text' => 'Approved'],
                ['value' => 'sent', 'text' => 'Sent'],
                ['value' => 'received', 'text' => 'Received'],
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
