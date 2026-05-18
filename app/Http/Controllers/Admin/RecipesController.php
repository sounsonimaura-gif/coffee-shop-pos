<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\MenuSize;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class RecipesController extends Controller
{
    public function index(): Response
    {
        $this->ensure('recipes.view');

        return Inertia::render('Recipes/Index', [
            'titleKey' => 'coffee.recipes',
            'routes' => [
                'data' => 'admin.recipes.data',
                'create' => 'admin.recipes.create',
                'edit' => 'admin.recipes.edit',
                'destroy' => 'admin.recipes.destroy',
            ],
            'columns' => [
                ['data' => 'menu_item_name', 'name' => 'menuItem.name', 'title' => 'coffee.menu_item'],
                ['data' => 'menu_size_name', 'name' => 'menuSize.name', 'title' => 'coffee.size'],
                ['data' => 'name', 'name' => 'name', 'title' => 'coffee.name'],
                ['data' => 'estimated_cost', 'name' => 'estimated_cost', 'title' => 'coffee.estimated_cost'],
                ['data' => 'is_active', 'name' => 'is_active', 'title' => 'coffee.is_active'],
            ],
            'permissions' => $this->permsForFront('recipes'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensure('recipes.view');

        $companyId = $request->user()->company_id;

        $query = Recipe::query()
            ->whereHas('menuItem', fn ($q) => $q->where('company_id', $companyId))
            ->with(['menuItem:id,name,company_id', 'menuSize:id,name']);

        return DataTables::of($query)
            ->addColumn('menu_item_name', fn ($p) => $p->menuItem?->name ?? '—')
            ->addColumn('menu_size_name', fn ($p) => $p->menuSize?->name ?? '—')
            ->addColumn('actions', function ($p) {
                return view('admin.partials.row_actions', [
                    'id' => $p->id,
                    'editRoute' => 'admin.recipes.edit',
                    'destroyRoute' => 'admin.recipes.destroy',
                    'canEdit' => $this->canPerm('recipes.update'),
                    'canDelete' => $this->canPerm('recipes.delete'),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): Response
    {
        $this->ensure('recipes.create');

        return Inertia::render('Recipes/Form', [
            'titleKey' => 'coffee.recipes',
            'isEdit' => false,
            'model' => (object) ['items' => []],
            'props' => $this->formProps(),
            'routes' => [
                'store' => 'admin.recipes.store',
                'index' => 'admin.recipes.index',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensure('recipes.create');

        $payload = $this->validateRecipe($request);

        DB::transaction(function () use ($payload) {
            $recipe = Recipe::create([
                'menu_item_id' => $payload['menu_item_id'],
                'menu_size_id' => $payload['menu_size_id'] ?? null,
                'name' => $payload['name'] ?? null,
                'estimated_cost' => 0,
                'is_default' => $payload['is_default'] ?? false,
                'is_active' => $payload['is_active'] ?? true,
            ]);
            $this->syncItems($recipe, $payload['items']);
        });

        return redirect()->route('admin.recipes.index')
            ->with('success', __('coffee.saved_successfully'));
    }

    public function edit(int $id): Response
    {
        $this->ensure('recipes.update');

        $recipe = Recipe::query()->with('items')->findOrFail($id);
        $model = $recipe->toArray();
        $model['items'] = $recipe->items->map(fn ($i) => [
            'ingredient_id' => $i->ingredient_id,
            'unit_id' => $i->unit_id,
            'quantity_used' => $i->quantity_used,
            'cost_per_unit' => $i->cost_per_unit,
            'is_required' => (bool) $i->is_required,
            'deduct_stock' => (bool) $i->deduct_stock,
        ])->all();

        return Inertia::render('Recipes/Form', [
            'titleKey' => 'coffee.recipes',
            'isEdit' => true,
            'model' => $model,
            'props' => $this->formProps(),
            'routes' => [
                'update' => ['admin.recipes.update', $id],
                'index' => 'admin.recipes.index',
            ],
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->ensure('recipes.update');

        $recipe = Recipe::query()->findOrFail($id);
        $payload = $this->validateRecipe($request);

        DB::transaction(function () use ($recipe, $payload) {
            $recipe->update([
                'menu_item_id' => $payload['menu_item_id'],
                'menu_size_id' => $payload['menu_size_id'] ?? null,
                'name' => $payload['name'] ?? null,
                'is_default' => $payload['is_default'] ?? false,
                'is_active' => $payload['is_active'] ?? true,
            ]);
            $recipe->items()->delete();
            $this->syncItems($recipe, $payload['items']);
        });

        return redirect()->route('admin.recipes.index')
            ->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->ensure('recipes.delete');

        $recipe = Recipe::query()->findOrFail($id);
        $recipe->delete();

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function syncItems(Recipe $recipe, array $items): void
    {
        $totalCost = 0;
        foreach ($items as $row) {
            $cost = (float) ($row['quantity_used'] ?? 0) * (float) ($row['cost_per_unit'] ?? 0);
            RecipeItem::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $row['ingredient_id'],
                'unit_id' => $row['unit_id'] ?? null,
                'quantity_used' => $row['quantity_used'],
                'cost_per_unit' => $row['cost_per_unit'] ?? 0,
                'total_cost' => $cost,
                'is_required' => $row['is_required'] ?? true,
                'deduct_stock' => $row['deduct_stock'] ?? true,
            ]);
            $totalCost += $cost;
        }
        $recipe->update(['estimated_cost' => $totalCost]);
    }

    protected function validateRecipe(Request $request): array
    {
        return $request->validate([
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'menu_size_id' => ['nullable', 'integer', 'exists:menu_sizes,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'items.*.unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'items.*.quantity_used' => ['required', 'numeric', 'min:0.0001'],
            'items.*.cost_per_unit' => ['nullable', 'numeric', 'min:0'],
            'items.*.is_required' => ['nullable', 'boolean'],
            'items.*.deduct_stock' => ['nullable', 'boolean'],
        ]);
    }

    protected function formProps(): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'menu_items' => MenuItem::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'menu_sizes' => MenuSize::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'ingredients' => Ingredient::query()->where('company_id', $companyId)->select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::query()->where('company_id', $companyId)->select('id', 'name', 'symbol')->orderBy('name')->get(),
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
