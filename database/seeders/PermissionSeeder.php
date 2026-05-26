<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Master list of permissions. Each module has a consistent set of
     * actions (view, create, update, delete). Special modules like
     * `pos` and `reports` get module-specific permissions.
     */
    public static array $modules = [
        'companies' => ['view', 'create', 'update', 'delete'],
        'branches' => ['view', 'create', 'update', 'delete'],
        'warehouses' => ['view', 'create', 'update', 'delete'],
        'pos_counters' => ['view', 'create', 'update', 'delete'],
        'kitchen_stations' => ['view', 'create', 'update', 'delete'],
        'table_floors' => ['view', 'create', 'update', 'delete'],
        'table_zones' => ['view', 'create', 'update', 'delete'],
        'dining_tables' => ['view', 'create', 'update', 'delete'],

        'users' => ['view', 'create', 'update', 'delete'],
        'roles' => ['view', 'create', 'update', 'delete'],
        'permissions' => ['view'],

        'menu_categories' => ['view', 'create', 'update', 'delete'],
        'menu_items' => ['view', 'create', 'update', 'delete'],
        'menu_sizes' => ['view', 'create', 'update', 'delete'],
        'menu_options' => ['view', 'create', 'update', 'delete'],
        'menu_addons' => ['view', 'create', 'update', 'delete'],

        'units' => ['view', 'create', 'update', 'delete'],
        'ingredient_categories' => ['view', 'create', 'update', 'delete'],
        'ingredients' => ['view', 'create', 'update', 'delete'],
        'recipes' => ['view', 'create', 'update', 'delete'],

        'suppliers' => ['view', 'create', 'update', 'delete'],
        'purchases' => ['view', 'create', 'update', 'delete', 'receive'],
        'supplier_payments' => ['view', 'create', 'update', 'delete'],

        'stock_batches' => ['view'],
        'stock_balances' => ['view'],
        'stock_movements' => ['view'],
        'stock_adjustments' => ['view', 'create', 'update', 'delete'],
        'stock_transfers' => ['view', 'create', 'update', 'delete', 'approve'],
        'waste_records' => ['view', 'create', 'update', 'delete'],
        'stock_alerts' => ['view'],

        'customers' => ['view', 'create', 'update', 'delete'],
        'membership_levels' => ['view', 'create', 'update', 'delete'],
        'promotions' => ['view', 'create', 'update', 'delete'],
        'coupons' => ['view', 'create', 'update', 'delete'],

        'cashier_shifts' => ['view', 'open', 'close'],
        'pos' => ['use'],
        'orders' => ['view', 'create', 'update', 'delete', 'void'],
        'sale_invoices' => ['view', 'create', 'update', 'delete', 'void'],
        'payments' => ['view', 'create', 'update', 'delete'],
        'online_orders' => ['view', 'create', 'update', 'delete'],
        'delivery_orders' => ['view', 'create', 'update', 'delete'],

        'expense_categories' => ['view', 'create', 'update', 'delete'],
        'expenses' => ['view', 'create', 'update', 'delete'],

        'staff' => ['view', 'create', 'update', 'delete'],
        'staff_schedules' => ['view', 'create', 'update', 'delete'],
        'payrolls' => ['view', 'create', 'update', 'delete'],
        'commissions' => ['view', 'create', 'update', 'delete'],

        'payment_methods' => ['view', 'create', 'update', 'delete'],
        'tax_rates' => ['view', 'create', 'update', 'delete'],
        'system_settings' => ['view', 'update'],
        'code_sequences' => ['view', 'update'],
        'notification_templates' => ['view', 'create', 'update', 'delete'],

        'reports' => ['sales', 'inventory', 'expenses', 'profit'],
        'report_exports' => ['view'],
        'audit_logs' => ['view'],
        'login_histories' => ['view'],
        'database_backups' => ['view', 'create', 'delete'],
        'notifications' => ['view'],
        'loyalty_point_transactions' => ['view'],
    ];

    public function run(): void
    {
        foreach (self::$modules as $module => $actions) {
            foreach ($actions as $action) {
                $name = "{$module}.{$action}";
                Permission::updateOrCreate(
                    ['name' => $name],
                    [
                        'module' => $module,
                        'label' => str_replace('_', ' ', ucfirst($module)).' - '.ucfirst($action),
                        'description' => "Permission to {$action} {$module}",
                    ]
                );
            }
        }
    }
}
