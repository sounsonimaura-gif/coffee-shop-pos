<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DiningTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('pos.use')), 403);

        $companyId = $user->company_id;
        $branchId = session('active_branch_id');

        return Inertia::render('Pos/Index', [
            'categories' => MenuCategory::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug']),
            'menuItems' => MenuItem::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->where('availability_status', 'available')
                ->orderBy('name')
                ->get(['id', 'category_id', 'menu_code', 'name', 'sale_price', 'image_path']),
            'tables' => DiningTable::query()
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->orderBy('table_no')
                ->get(['id', 'table_no', 'name', 'status', 'capacity']),
            'customers' => Customer::query()
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->orderBy('name')
                ->limit(200)
                ->get(['id', 'name', 'phone', 'customer_code']),
            'paymentMethods' => PaymentMethod::query()
                ->where(function ($q) use ($companyId) {
                    $q->whereNull('company_id')->orWhere('company_id', $companyId);
                })
                ->where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->get(['id', 'code', 'name', 'type', 'is_default']),
        ]);
    }
}
