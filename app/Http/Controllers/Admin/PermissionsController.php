<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class PermissionsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Permissions/Index', [
            'titleKey' => 'coffee.permissions',
            'routes' => [
                'data' => 'admin.permissions.data',
            ],
            'columns' => [
                ['data' => 'module', 'name' => 'module', 'title' => 'coffee.module', 'orderable' => true, 'searchable' => true],
                ['data' => 'name', 'name' => 'name', 'title' => 'coffee.code', 'orderable' => true, 'searchable' => true],
                ['data' => 'label', 'name' => 'label', 'title' => 'coffee.name', 'orderable' => true, 'searchable' => true],
                ['data' => 'description', 'name' => 'description', 'title' => 'coffee.description', 'orderable' => true, 'searchable' => true],
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('permissions.view')), 403);

        return DataTables::of(Permission::query())->toJson();
    }
}
