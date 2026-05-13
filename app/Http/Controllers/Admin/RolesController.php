<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends BaseCrudController
{
    protected string $model = Role::class;

    protected string $viewNamespace = 'Roles';

    protected string $permissionPrefix = 'roles';

    protected string $routePrefix = 'admin.roles';

    protected string $titleKey = 'coffee.roles';

    protected function columns(): array
    {
        return [
            ['data' => 'name', 'title' => 'coffee.name'],
            ['data' => 'description', 'title' => 'coffee.description'],
            ['data' => 'is_system', 'title' => 'coffee.is_active'],
            ['data' => 'is_active', 'title' => 'coffee.is_active'],
        ];
    }

    protected function baseQuery(): Builder
    {
        $cid = request()->user()?->company_id;

        return Role::query()->where(function ($q) use ($cid) {
            $q->whereNull('company_id')->orWhere('company_id', $cid);
        });
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100',
                Rule::unique('roles', 'name')->ignore($model?->id)->where(fn ($q) => $q->where('company_id', $request->user()?->company_id)),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorizeAction('view');
        $query = $this->baseQuery();
        $dt = DataTables::of($query);
        $dt->addColumn('actions', fn ($r) => view('admin.partials.row_actions', [
            'id' => $r->id,
            'editRoute' => 'admin.roles.edit',
            'destroyRoute' => 'admin.roles.destroy',
            'canEdit' => $this->userHas('update') && ! $r->is_system,
            'canDelete' => $this->userHas('delete') && ! $r->is_system,
        ])->render());
        $dt->rawColumns(['actions']);

        return $dt->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');
        $payload = $request->validate($this->rules($request));
        $permissionIds = $payload['permission_ids'] ?? [];
        unset($payload['permission_ids']);
        $payload['company_id'] = $request->user()?->company_id;
        $payload['guard_name'] = 'web';

        DB::transaction(function () use ($payload, $permissionIds) {
            $role = Role::create($payload);
            $role->permissions()->sync($permissionIds);
        });

        return redirect()->route('admin.roles.index')->with('success', __('coffee.saved_successfully'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAction('update');
        $role = $this->baseQuery()->findOrFail($id);

        abort_if($role->is_system, 403, __('coffee.no_permission'));

        $payload = $request->validate($this->rules($request, $role));
        $permissionIds = $payload['permission_ids'] ?? [];
        unset($payload['permission_ids']);

        DB::transaction(function () use ($role, $payload, $permissionIds) {
            $role->update($payload);
            $role->permissions()->sync($permissionIds);
        });

        return redirect()->route('admin.roles.index')->with('success', __('coffee.updated_successfully'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAction('delete');
        $role = $this->baseQuery()->findOrFail($id);
        abort_if($role->is_system, 403, __('coffee.no_permission'));
        $role->delete();

        return back()->with('success', __('coffee.deleted_successfully'));
    }

    protected function formProps(?object $model = null): array
    {
        return [
            'permissions' => Permission::query()
                ->orderBy('module')->orderBy('name')
                ->get(['id', 'module', 'name', 'label'])
                ->groupBy('module'),
        ];
    }

    protected function transformForForm(object $model): array
    {
        return array_merge($model->toArray(), [
            'permission_ids' => $model->permissions()->pluck('permissions.id')->all(),
        ]);
    }
}
