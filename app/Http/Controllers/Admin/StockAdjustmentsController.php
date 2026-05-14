<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class StockAdjustmentsController extends Controller
{
    public function __construct(protected StockService $stock) {}

    public function index(): Response
    {
        $this->ensure('stock_adjustments.view');

        return Inertia::render('StockAdjustments/Index', [
            'titleKey' => 'coffee.stock_adjustments',
            'routes' => [
                'data' => 'admin.stock-adjustments.data',
                'create' => 'admin.stock-adjustments.create',
                'edit' => 'admin.stock-adjustments.edit',
                'destroy' => 'admin.stock-adjustments.destroy',
            ],
            'columns' => [
                ['data' => 'adjusted_at', 'name' => 'adjusted_at', 'title' => __('coffee.date')],
                ['data' => 'ingredient_name', 'name' => 'ingredient.name', 'title' => __('coffee.ingredient')],
                ['data' => 'adjustment_type', 'name' => 'adjustment_type', 'title' => __('coffee.adjustment_type')],
                ['data' => 'quantity', 'name' => 'quantity', 'title' => __('coffee.quantity')],
                ['data' => 'reason', 'name' => 'reason', 'title' => __('coffee.reason')],
            ],
            'permissions' => $this->permsForFront('stock_adjustments'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensure('stock_adjustments.view');

        $query = StockAdjustment::query()
            ->with('ingredient:id,name')
            ->where('company_id', $request->user()->company_id);

        return DataTables::of($query)
            ->addColumn('ingredient_name', fn ($p) => $p->ingredient?->name ?? '—')
            ->addColumn('actions', function ($p) {
                return view('admin.partials.row_actions', [
                    'id' => $p->id,
                    'editRoute' => 'admin.stock-adjustments.edit',
                    'destroyRoute' => 'admin.stock-adjustments.destroy',
                    'canEdit' => $this->canPerm('stock_adjustments.update'),
                    'canDelete' => $this->canPerm('stock_adjustments.delete'),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): Response
    {
        $this->ensure('stock_adjustments.create');

        return Inertia::render('StockAdjustments/Form', [
            'titleKey' => 'coffee.stock_adjustments',
            'isEdit' => false,
            'model' => (object) [],
            'props' => $this->formProps(),
            'routes' => [
                'store' => 'admin.stock-adjustments.store',
                'index' => 'admin.stock-adjustments.index',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensure('stock_adjustments.create');

        $payload = $this->validateData($request);

        DB::transaction(function () use ($payload, $request) {
            $adj = StockAdjustment::create([
                'company_id' => $request->user()->company_id,
                'branch_id' => $payload['branch_id'] ?? session('active_branch_id'),
                'warehouse_id' => $payload['warehouse_id'] ?? null,
                'ingredient_id' => $payload['ingredient_id'],
                'created_by' => $request->user()->id,
                'adjustment_type' => $payload['adjustment_type'],
                'quantity' => $payload['quantity'],
                'reason' => $payload['reason'] ?? null,
                'note' => $payload['note'] ?? null,
                'adjusted_at' => $payload['adjusted_at'] ?? now(),
            ]);

            $this->stock->record([
                'company_id' => $adj->company_id,
                'branch_id' => $adj->branch_id,
                'warehouse_id' => $adj->warehouse_id,
                'ingredient_id' => $adj->ingredient_id,
                'created_by' => $request->user()->id,
                'movement_type' => $payload['adjustment_type'] === 'increase' ? 'adjustment_in' : 'adjustment_out',
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adj->id,
                'quantity_in' => $payload['adjustment_type'] === 'increase' ? $payload['quantity'] : 0,
                'quantity_out' => $payload['adjustment_type'] === 'decrease' ? $payload['quantity'] : 0,
                'note' => $payload['reason'] ?? null,
            ]);
        });

        return redirect()->route('admin.stock-adjustments.index')
            ->with('success', __('coffee.saved_successfully'));
    }

    public function edit(int $id): Response
    {
        $this->ensure('stock_adjustments.update');

        $adj = StockAdjustment::query()
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);

        return Inertia::render('StockAdjustments/Form', [
            'titleKey' => 'coffee.stock_adjustments',
            'isEdit' => true,
            'model' => $adj->toArray(),
            'props' => $this->formProps(),
            'routes' => [
                'update' => ['admin.stock-adjustments.update', $id],
                'index' => 'admin.stock-adjustments.index',
            ],
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->ensure('stock_adjustments.update');

        $adj = StockAdjustment::query()
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $payload = $this->validateData($request);
        $adj->update([
            'branch_id' => $payload['branch_id'] ?? $adj->branch_id,
            'warehouse_id' => $payload['warehouse_id'] ?? $adj->warehouse_id,
            'ingredient_id' => $payload['ingredient_id'],
            'adjustment_type' => $payload['adjustment_type'],
            'quantity' => $payload['quantity'],
            'reason' => $payload['reason'] ?? null,
            'note' => $payload['note'] ?? null,
            'adjusted_at' => $payload['adjusted_at'] ?? $adj->adjusted_at,
        ]);

        return redirect()->route('admin.stock-adjustments.index')
            ->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->ensure('stock_adjustments.delete');

        $adj = StockAdjustment::query()
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);
        $adj->delete();

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'adjustment_type' => ['required', 'in:increase,decrease'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'adjusted_at' => ['nullable', 'date'],
        ]);
    }

    protected function formProps(): array
    {
        return [
            'warehouses' => Warehouse::query()->select('id', 'name')->orderBy('name')->get(),
            'ingredients' => Ingredient::query()->select('id', 'name')->orderBy('name')->get(),
            'adjustment_types' => [
                ['value' => 'increase', 'text' => 'Increase'],
                ['value' => 'decrease', 'text' => 'Decrease'],
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
