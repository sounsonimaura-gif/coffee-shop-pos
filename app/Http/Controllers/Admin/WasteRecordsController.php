<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Warehouse;
use App\Models\WasteRecord;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class WasteRecordsController extends Controller
{
    public function __construct(protected StockService $stock) {}

    public function index(): Response
    {
        $this->ensure('waste_records.view');

        return Inertia::render('WasteRecords/Index', [
            'titleKey' => 'coffee.waste_records',
            'routes' => [
                'data' => 'admin.waste-records.data',
                'create' => 'admin.waste-records.create',
                'edit' => 'admin.waste-records.edit',
                'destroy' => 'admin.waste-records.destroy',
            ],
            'columns' => [
                ['data' => 'waste_date', 'name' => 'waste_date', 'title' => 'coffee.date'],
                ['data' => 'ingredient_name', 'name' => 'ingredient.name', 'title' => 'coffee.ingredient'],
                ['data' => 'waste_type', 'name' => 'waste_type', 'title' => 'coffee.type'],
                ['data' => 'quantity', 'name' => 'quantity', 'title' => 'coffee.quantity'],
                ['data' => 'cost_amount', 'name' => 'cost_amount', 'title' => 'coffee.cost'],
                ['data' => 'reason', 'name' => 'reason', 'title' => 'coffee.reason'],
            ],
            'permissions' => $this->permsForFront('waste_records'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensure('waste_records.view');

        $query = WasteRecord::query()
            ->with('ingredient:id,name')
            ->where('company_id', $request->user()->company_id);

        return DataTables::of($query)
            ->addColumn('ingredient_name', fn ($p) => $p->ingredient?->name ?? '—')
            ->addColumn('actions', function ($p) {
                return view('admin.partials.row_actions', [
                    'id' => $p->id,
                    'editRoute' => 'admin.waste-records.edit',
                    'destroyRoute' => 'admin.waste-records.destroy',
                    'canEdit' => $this->canPerm('waste_records.update'),
                    'canDelete' => $this->canPerm('waste_records.delete'),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): Response
    {
        $this->ensure('waste_records.create');

        return Inertia::render('WasteRecords/Form', [
            'titleKey' => 'coffee.waste_records',
            'isEdit' => false,
            'model' => (object) [],
            'props' => $this->formProps(),
            'routes' => [
                'store' => 'admin.waste-records.store',
                'index' => 'admin.waste-records.index',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensure('waste_records.create');

        $payload = $this->validateData($request);

        DB::transaction(function () use ($payload, $request) {
            $waste = WasteRecord::create([
                'company_id' => $request->user()->company_id,
                'branch_id' => $payload['branch_id'] ?? session('active_branch_id'),
                'warehouse_id' => $payload['warehouse_id'] ?? null,
                'ingredient_id' => $payload['ingredient_id'],
                'created_by' => $request->user()->id,
                'quantity' => $payload['quantity'],
                'cost_amount' => $payload['cost_amount'] ?? 0,
                'waste_type' => $payload['waste_type'] ?? 'waste',
                'reason' => $payload['reason'] ?? null,
                'waste_date' => $payload['waste_date'] ?? now()->toDateString(),
            ]);

            $this->stock->record([
                'company_id' => $waste->company_id,
                'branch_id' => $waste->branch_id,
                'warehouse_id' => $waste->warehouse_id,
                'ingredient_id' => $waste->ingredient_id,
                'created_by' => $request->user()->id,
                'movement_type' => match ($waste->waste_type) {
                    'expired' => 'expired',
                    'damaged' => 'damaged',
                    default => 'waste',
                },
                'reference_type' => WasteRecord::class,
                'reference_id' => $waste->id,
                'quantity_in' => 0,
                'quantity_out' => $payload['quantity'],
                'note' => $payload['reason'] ?? null,
            ]);
        });

        return redirect()->route('admin.waste-records.index')
            ->with('success', __('coffee.saved_successfully'));
    }

    public function edit(int $id): Response
    {
        $this->ensure('waste_records.update');

        $waste = WasteRecord::query()
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);

        return Inertia::render('WasteRecords/Form', [
            'titleKey' => 'coffee.waste_records',
            'isEdit' => true,
            'model' => $waste->toArray(),
            'props' => $this->formProps(),
            'routes' => [
                'update' => ['admin.waste-records.update', $id],
                'index' => 'admin.waste-records.index',
            ],
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->ensure('waste_records.update');

        $waste = WasteRecord::query()
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($id);

        $payload = $this->validateData($request);
        $waste->update([
            'branch_id' => $payload['branch_id'] ?? $waste->branch_id,
            'warehouse_id' => $payload['warehouse_id'] ?? $waste->warehouse_id,
            'ingredient_id' => $payload['ingredient_id'],
            'quantity' => $payload['quantity'],
            'cost_amount' => $payload['cost_amount'] ?? 0,
            'waste_type' => $payload['waste_type'] ?? $waste->waste_type,
            'reason' => $payload['reason'] ?? null,
            'waste_date' => $payload['waste_date'] ?? $waste->waste_date,
        ]);

        return redirect()->route('admin.waste-records.index')
            ->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->ensure('waste_records.delete');

        $waste = WasteRecord::query()
            ->where('company_id', request()->user()->company_id)
            ->findOrFail($id);
        $waste->delete();

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'cost_amount' => ['nullable', 'numeric', 'min:0'],
            'waste_type' => ['nullable', 'in:waste,expired,damaged,loss,other'],
            'reason' => ['nullable', 'string'],
            'waste_date' => ['nullable', 'date'],
        ]);
    }

    protected function formProps(): array
    {
        return [
            'warehouses' => Warehouse::query()->select('id', 'name')->orderBy('name')->get(),
            'ingredients' => Ingredient::query()->select('id', 'name')->orderBy('name')->get(),
            'waste_types' => [
                ['value' => 'waste', 'text' => 'Waste'],
                ['value' => 'expired', 'text' => 'Expired'],
                ['value' => 'damaged', 'text' => 'Damaged'],
                ['value' => 'loss', 'text' => 'Loss'],
                ['value' => 'other', 'text' => 'Other'],
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
