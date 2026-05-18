<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\LoginHistory;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;

class LoginHistoriesController extends BaseReadOnlyController
{
    protected string $model = LoginHistory::class;

    protected string $viewNamespace = 'LoginHistories';

    protected string $permissionPrefix = 'login_histories';

    protected string $routePrefix = 'admin.login-histories';

    protected string $titleKey = 'coffee.login_histories';

    protected function columns(): array
    {
        return [
            ['data' => 'logged_in_at', 'title' => 'coffee.logged_at'],
            ['data' => 'user_name', 'name' => 'user.name', 'title' => 'coffee.user'],
            ['data' => 'ip_address', 'title' => 'coffee.ip_address'],
            ['data' => 'device', 'title' => 'coffee.user_agent'],
            ['data' => 'is_success', 'title' => 'coffee.status'],
        ];
    }

    protected function baseQuery(): Builder
    {
        return LoginHistory::query()->with('user');
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt
            ->addColumn('user_name', fn ($l) => $l->user?->name ?? '-')
            ->editColumn('logged_in_at', fn ($l) => optional($l->logged_in_at)?->format('Y-m-d H:i'))
            ->editColumn('is_success', fn ($l) => $l->is_success
                ? '<span class="badge bg-success">'.__('coffee.success').'</span>'
                : '<span class="badge bg-danger">'.__('coffee.failed').'</span>'
            )
            ->rawColumns(['is_success']);
    }
}
