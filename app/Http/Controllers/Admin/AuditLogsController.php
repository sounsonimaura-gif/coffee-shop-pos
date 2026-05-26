<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class AuditLogsController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('audit_logs.view')), 403);

        return Inertia::render('AuditLogs/Index', [
            'titleKey' => 'coffee.audit_logs',
            'routes' => ['data' => 'admin.audit-logs.data'],
            'columns' => [
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'coffee.from'],
                ['data' => 'event', 'name' => 'event', 'title' => 'coffee.type'],
                ['data' => 'auditable_type', 'name' => 'auditable_type', 'title' => 'coffee.module'],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'coffee.user'],
                ['data' => 'ip_address', 'name' => 'ip_address', 'title' => 'coffee.code'],
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::of(AuditLog::query())
            ->editColumn('created_at', fn ($a) => $a->created_at?->format('Y-m-d H:i'))
            ->toJson();
    }
}
