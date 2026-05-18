<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Facades\DataTables;

/**
 * Base CRUD controller for all the "simple" admin resources in this
 * project. Subclasses just declare a model class, a Vue page namespace,
 * and the columns/fields. The base class wires:
 *
 * - index() → Vue Index page with a Yajra DataTable server-side endpoint
 * - data()  → JSON DataTable response (server-side processing)
 * - create() / store() / edit() / update() / destroy() → standard CRUD
 *
 * Each controller is automatically permission-gated using the
 * `permissionPrefix` (e.g. `menu_items` -> menu_items.view / .create / ...).
 */
abstract class BaseCrudController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /**
     * Inertia page namespace, e.g. "MenuItems" => Pages/MenuItems/{Index,Form}
     */
    protected string $viewNamespace;

    /**
     * Permission module prefix (e.g. "menu_items"). Used to gate every
     * action automatically.
     */
    protected string $permissionPrefix;

    /**
     * Route name prefix (e.g. "admin.menu-items"). Index / Create / Edit
     * / Update / Destroy routes are derived from this.
     */
    protected string $routePrefix;

    /**
     * The page title shown in breadcrumbs. Subclasses may translate it
     * via i18n keys.
     */
    protected string $titleKey;

    /**
     * Define the DataTable columns. Each entry should be:
     *   ['data' => 'id', 'title' => 'ID']
     * Optionally include a 'render' closure (server-side) — we cannot
     * serialize JS closures here, so use raw_columns for HTML cells.
     */
    abstract protected function columns(): array;

    /**
     * Validation rules for store/update.
     */
    abstract protected function rules(Request $request, $model = null): array;

    /**
     * Optionally augment a DataTable response (e.g. add computed columns).
     */
    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt;
    }

    /**
     * Optionally scope the base query (e.g. by company / branch).
     */
    protected function baseQuery(): Builder
    {
        /** @var Builder $q */
        $q = $this->model::query();

        $companyId = request()->user()?->company_id;

        if ($companyId && $this->hasColumn('company_id')) {
            $q->where($this->qualifiedColumn('company_id'), $companyId);
        }

        return $q;
    }

    /**
     * Optionally provide form props (select options) for create/edit.
     */
    protected function formProps(?object $model = null): array
    {
        return [];
    }

    /**
     * Optionally transform the model before sending to the form view.
     */
    protected function transformForForm(object $model): array
    {
        return $model->toArray();
    }

    /**
     * Optionally augment the validated payload before save.
     */
    protected function beforeSave(array $payload, Request $request, ?object $model = null): array
    {
        if ($this->hasColumn('company_id') && empty($payload['company_id'])) {
            $payload['company_id'] = $request->user()?->company_id;
        }

        return $payload;
    }

    public function index(): Response
    {
        $this->authorizeAction('view');

        return Inertia::render("{$this->viewNamespace}/Index", [
            'titleKey' => $this->titleKey,
            'routes' => [
                'data' => $this->routeName('data'),
                'create' => $this->routeName('create'),
                'edit' => $this->routeName('edit'),
                'store' => $this->routeName('store'),
                'destroy' => $this->routeName('destroy'),
            ],
            'columns' => collect($this->columns())->map(fn ($c) => [
                'data' => $c['data'],
                'name' => $c['name'] ?? $c['data'],
                // Send the raw translation key so the Vue layer can re-translate
                // column titles when the user switches language without a full reload.
                'title' => $c['title'],
                'orderable' => $c['orderable'] ?? true,
                'searchable' => $c['searchable'] ?? true,
            ])->values()->all(),
            'permissions' => $this->permissionsForFront(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorizeAction('view');

        $query = $this->baseQuery();

        $dt = DataTables::of($query);

        $dt->addColumn('actions', function ($model) {
            return view('admin.partials.row_actions', [
                'id' => $model->id,
                'editRoute' => $this->routeName('edit'),
                'destroyRoute' => $this->routeName('destroy'),
                'canEdit' => $this->userHas('update'),
                'canDelete' => $this->userHas('delete'),
            ])->render();
        });

        $dt->rawColumns(['actions']);

        $dt = $this->transformDataTable($dt);

        return $dt->make(true);
    }

    public function create(): Response
    {
        $this->authorizeAction('create');

        return Inertia::render("{$this->viewNamespace}/Form", [
            'titleKey' => $this->titleKey,
            'isEdit' => false,
            'model' => (object) [],
            'props' => $this->formProps(),
            'routes' => [
                'store' => $this->routeName('store'),
                'index' => $this->routeName('index'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');

        $payload = $request->validate($this->rules($request));
        $payload = $this->beforeSave($payload, $request);

        DB::transaction(function () use ($payload) {
            $this->model::create($payload);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', __('coffee.saved_successfully'));
    }

    public function edit(int $id): Response
    {
        $this->authorizeAction('update');

        $model = $this->baseQuery()->findOrFail($id);

        return Inertia::render("{$this->viewNamespace}/Form", [
            'titleKey' => $this->titleKey,
            'isEdit' => true,
            'model' => $this->transformForForm($model),
            'props' => $this->formProps($model),
            'routes' => [
                'update' => [$this->routeName('update'), $id],
                'index' => $this->routeName('index'),
            ],
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAction('update');

        $model = $this->baseQuery()->findOrFail($id);

        $payload = $request->validate($this->rules($request, $model));
        $payload = $this->beforeSave($payload, $request, $model);

        DB::transaction(function () use ($payload, $model) {
            $model->update($payload);
        });

        return redirect()
            ->route($this->routeName('index'))
            ->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAction('delete');

        $model = $this->baseQuery()->findOrFail($id);

        DB::transaction(fn () => $model->delete());

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    // --- helpers ----------------------------------------------------

    protected function routeName(string $action): string
    {
        return match ($action) {
            'data' => "{$this->routePrefix}.data",
            default => "{$this->routePrefix}.{$action}",
        };
    }

    protected function authorizeAction(string $action): void
    {
        $user = request()->user();
        if (! $user) {
            abort(401);
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        $perm = "{$this->permissionPrefix}.{$action}";
        if (! $user->hasPermission($perm)) {
            abort(403, __('coffee.no_permission'));
        }
    }

    protected function userHas(string $action): bool
    {
        $user = request()->user();
        if (! $user) {
            return false;
        }
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->hasPermission("{$this->permissionPrefix}.{$action}");
    }

    protected function permissionsForFront(): array
    {
        return [
            'view' => $this->userHas('view'),
            'create' => $this->userHas('create'),
            'update' => $this->userHas('update'),
            'delete' => $this->userHas('delete'),
        ];
    }

    protected function hasColumn(string $column): bool
    {
        return in_array($column, (new $this->model)->getFillable(), true);
    }

    protected function qualifiedColumn(string $column): string
    {
        return (new $this->model)->getTable().'.'.$column;
    }
}
