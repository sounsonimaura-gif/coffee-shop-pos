<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends BaseCrudController
{
    protected string $model = User::class;

    protected string $viewNamespace = 'Users';

    protected string $permissionPrefix = 'users';

    protected string $routePrefix = 'admin.users';

    protected string $titleKey = 'coffee.users';

    protected function columns(): array
    {
        return [
            ['data' => 'id', 'title' => 'coffee.code'],
            ['data' => 'name', 'title' => 'coffee.name'],
            ['data' => 'email', 'title' => 'coffee.email'],
            ['data' => 'phone', 'title' => 'coffee.phone'],
            ['data' => 'role_name', 'title' => 'coffee.role'],
            ['data' => 'status', 'title' => 'coffee.status'],
        ];
    }

    protected function rules(Request $request, $model = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($model?->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'default_branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'password' => [$model ? 'nullable' : 'required', 'nullable', 'string', 'min:6'],
            'status' => ['required', 'in:active,inactive,blocked'],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['integer', 'exists:branches,id'],
        ];
    }

    protected function beforeSave(array $payload, Request $request, ?object $model = null): array
    {
        $payload = parent::beforeSave($payload, $request, $model);

        if (! empty($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            unset($payload['password']);
        }

        return $payload;
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorizeAction('view');

        $query = $this->baseQuery()
            ->with('role:id,name')
            ->select('users.*');

        $dt = DataTables::of($query);
        $dt->addColumn('role_name', fn ($u) => $u->role?->name ?? '-');
        $dt->addColumn('actions', fn ($u) => view('admin.partials.row_actions', [
            'id' => $u->id,
            'editRoute' => 'admin.users.edit',
            'destroyRoute' => 'admin.users.destroy',
            'canEdit' => $this->userHas('update'),
            'canDelete' => $this->userHas('delete'),
        ])->render());
        $dt->rawColumns(['actions']);

        return $dt->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAction('create');
        $payload = $request->validate($this->rules($request));
        $branchIds = $payload['branch_ids'] ?? [];
        unset($payload['branch_ids']);

        $payload = $this->beforeSave($payload, $request);

        DB::transaction(function () use ($payload, $branchIds) {
            $user = User::create($payload);
            if (! empty($branchIds)) {
                $user->branches()->sync(array_fill_keys($branchIds, ['can_access' => true]));
            }
        });

        return redirect()->route('admin.users.index')->with('success', __('coffee.saved_successfully'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAction('update');
        $user = $this->baseQuery()->findOrFail($id);

        $payload = $request->validate($this->rules($request, $user));
        $branchIds = $payload['branch_ids'] ?? [];
        unset($payload['branch_ids']);

        $payload = $this->beforeSave($payload, $request, $user);

        DB::transaction(function () use ($user, $payload, $branchIds) {
            $user->update($payload);
            $user->branches()->sync(array_fill_keys($branchIds, ['can_access' => true]));
        });

        return redirect()->route('admin.users.index')->with('success', __('coffee.updated_successfully'));
    }

    protected function formProps(?object $model = null): array
    {
        $companyId = request()->user()?->company_id;

        return [
            'roles' => Role::query()
                ->where(function ($q) use ($companyId) {
                    $q->whereNull('company_id')->orWhere('company_id', $companyId);
                })
                ->orderBy('name')
                ->get(['id', 'name']),
            'branches' => Branch::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'statuses' => [
                ['value' => 'active', 'text' => 'Active'],
                ['value' => 'inactive', 'text' => 'Inactive'],
                ['value' => 'blocked', 'text' => 'Blocked'],
            ],
        ];
    }

    protected function transformForForm(object $model): array
    {
        return array_merge($model->only(['id', 'company_id', 'role_id', 'default_branch_id', 'name', 'email', 'phone', 'status']), [
            'branch_ids' => $model->branches()->pluck('branches.id')->all(),
        ]);
    }
}
