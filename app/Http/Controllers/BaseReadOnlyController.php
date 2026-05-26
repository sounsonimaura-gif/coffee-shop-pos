<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Facades\DataTables;

/**
 * Read-only counterpart of BaseCrudController. Used for the system /
 * generated tables (notifications, login_histories, stock_alerts,
 * loyalty_point_transactions, code_sequences, database_backups,
 * report_exports) — they appear in the admin UI as Yajra DataTable
 * lists but are not user-editable from the front-end.
 *
 * Subclasses only need to declare:
 *   - model class
 *   - view namespace (e.g. "LoginHistories")
 *   - permission prefix (e.g. "login_histories")
 *   - title translation key (e.g. "coffee.login_histories")
 *   - columns()
 */
abstract class BaseReadOnlyController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    protected string $viewNamespace;

    protected string $permissionPrefix;

    protected string $routePrefix;

    protected string $titleKey;

    abstract protected function columns(): array;

    protected function baseQuery(): Builder
    {
        /** @var Builder $q */
        $q = $this->model::query();

        $companyId = request()->user()?->company_id;

        if ($companyId && $this->hasColumn('company_id')) {
            $q->where('company_id', $companyId);
        }

        return $q;
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt;
    }

    public function index(): Response
    {
        $this->authorizeView();

        return Inertia::render("{$this->viewNamespace}/Index", [
            'titleKey' => $this->titleKey,
            'routes' => [
                'data' => "{$this->routePrefix}.data",
            ],
            'columns' => collect($this->columns())->map(fn ($c) => [
                'data' => $c['data'],
                'name' => $c['name'] ?? $c['data'],
                'title' => $c['title'],
                'orderable' => $c['orderable'] ?? true,
                'searchable' => $c['searchable'] ?? true,
            ])->values()->all(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorizeView();

        $dt = DataTables::of($this->baseQuery());
        $dt = $this->transformDataTable($dt);

        return $dt->toJson();
    }

    protected function authorizeView(): void
    {
        $user = request()->user();
        abort_unless(
            $user && ($user->isSuperAdmin() || $user->hasPermission("{$this->permissionPrefix}.view")),
            403
        );
    }

    protected function hasColumn(string $column): bool
    {
        /** @var Model $instance */
        $instance = new $this->model;

        return $instance->getConnection()
            ->getSchemaBuilder()
            ->hasColumn($instance->getTable(), $column);
    }
}
