<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseReadOnlyController;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;

class NotificationsController extends BaseReadOnlyController
{
    protected string $model = Notification::class;

    protected string $viewNamespace = 'Notifications';

    protected string $permissionPrefix = 'notifications';

    protected string $routePrefix = 'admin.notifications';

    protected string $titleKey = 'coffee.notifications';

    protected function columns(): array
    {
        return [
            ['data' => 'created_at', 'title' => 'coffee.sent_at'],
            ['data' => 'type', 'title' => 'coffee.type'],
            ['data' => 'title', 'title' => 'coffee.title', 'orderable' => false],
            ['data' => 'body', 'title' => 'coffee.body', 'orderable' => false, 'searchable' => false],
            ['data' => 'read_at', 'title' => 'coffee.status'],
        ];
    }

    protected function baseQuery(): Builder
    {
        // The notifications table is a polymorphic store; we scope to the
        // current user only, which mirrors Laravel's default UI for it.
        return Notification::query()
            ->where('notifiable_type', request()->user()->getMorphClass())
            ->where('notifiable_id', request()->user()?->id);
    }

    protected function transformDataTable(DataTableAbstract $dt): DataTableAbstract
    {
        return $dt
            ->editColumn('created_at', fn ($n) => $n->created_at?->format('Y-m-d H:i'))
            ->editColumn('title', function ($n) {
                $data = is_array($n->data) ? $n->data : (json_decode($n->data, true) ?: []);

                return e($data['title'] ?? '');
            })
            ->editColumn('body', function ($n) {
                $data = is_array($n->data) ? $n->data : (json_decode($n->data, true) ?: []);

                return e($data['body'] ?? $data['message'] ?? '');
            })
            ->editColumn('read_at', fn ($n) => $n->read_at
                ? '<span class="badge bg-secondary">'.__('coffee.read').'</span>'
                : '<span class="badge bg-success">'.__('coffee.unread').'</span>'
            )
            ->rawColumns(['read_at']);
    }
}
