<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Seed master permissions
            $this->call(PermissionSeeder::class);

            // 2. Create demo company
            $company = Company::firstOrCreate(
                ['company_code' => 'CO-001'],
                [
                    'name' => 'Devin Coffee Co.',
                    'owner_name' => 'Soni Maura',
                    'phone' => '+855 12 000 000',
                    'email' => 'owner@coffee.test',
                    'website' => 'https://coffee.test',
                    'address' => 'Phnom Penh, Cambodia',
                    'currency_code' => 'USD',
                    'language_code' => 'en',
                    'status' => 'active',
                ]
            );

            // 3. Super Admin role with all permissions
            $superAdmin = Role::firstOrCreate(
                ['company_id' => null, 'name' => 'Super Admin'],
                [
                    'guard_name' => 'web',
                    'description' => 'Has unrestricted access to every module.',
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
            $superAdmin->permissions()->sync(Permission::pluck('id')->all());

            // 4. Branch Manager role
            $manager = Role::firstOrCreate(
                ['company_id' => $company->id, 'name' => 'Branch Manager'],
                [
                    'guard_name' => 'web',
                    'description' => 'Manages a single branch (inventory, staff, sales).',
                    'is_system' => false,
                    'is_active' => true,
                ]
            );
            $manager->permissions()->sync(
                Permission::whereIn('module', [
                    'menu_categories', 'menu_items', 'menu_sizes', 'menu_options', 'menu_addons',
                    'ingredients', 'units', 'suppliers', 'purchases', 'supplier_payments',
                    'stock_batches', 'stock_balances', 'stock_movements', 'stock_adjustments',
                    'stock_transfers', 'waste_records', 'stock_alerts',
                    'customers', 'membership_levels', 'promotions', 'coupons',
                    'cashier_shifts', 'pos', 'orders', 'sale_invoices', 'payments',
                    'online_orders', 'delivery_orders',
                    'expense_categories', 'expenses', 'reports',
                    'dining_tables', 'table_floors', 'table_zones', 'pos_counters', 'kitchen_stations',
                ])->pluck('id')->all()
            );

            // 5. Cashier role
            $cashier = Role::firstOrCreate(
                ['company_id' => $company->id, 'name' => 'Cashier'],
                [
                    'guard_name' => 'web',
                    'description' => 'Operates the POS, handles payments and shifts.',
                    'is_system' => false,
                    'is_active' => true,
                ]
            );
            $cashier->permissions()->sync(
                Permission::whereIn('name', [
                    'pos.use',
                    'orders.view', 'orders.create', 'orders.update',
                    'sale_invoices.view', 'sale_invoices.create',
                    'payments.view', 'payments.create',
                    'cashier_shifts.open', 'cashier_shifts.close', 'cashier_shifts.view',
                    'customers.view', 'customers.create',
                    'dining_tables.view',
                    'menu_items.view', 'menu_categories.view',
                ])->pluck('id')->all()
            );

            // 6. Demo main branch
            $branch = Branch::firstOrCreate(
                ['company_id' => $company->id, 'branch_code' => 'BR-001'],
                [
                    'name' => 'Main Branch',
                    'address' => '#1 Coffee Street, Phnom Penh',
                    'phone' => '+855 12 111 111',
                    'is_main_branch' => true,
                    'status' => 'active',
                ]
            );

            // 7. Default super admin user
            $admin = User::firstOrCreate(
                ['email' => 'admin@coffee.test'],
                [
                    'company_id' => $company->id,
                    'role_id' => $superAdmin->id,
                    'default_branch_id' => $branch->id,
                    'name' => 'Super Admin',
                    'phone' => '+855 12 000 000',
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $admin->branches()->syncWithoutDetaching([$branch->id => ['is_default' => true, 'can_access' => true]]);

            // 8. Demo branch manager
            $managerUser = User::firstOrCreate(
                ['email' => 'manager@coffee.test'],
                [
                    'company_id' => $company->id,
                    'role_id' => $manager->id,
                    'default_branch_id' => $branch->id,
                    'name' => 'Demo Manager',
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $managerUser->branches()->syncWithoutDetaching([$branch->id => ['is_default' => true, 'can_access' => true]]);

            // 9. Demo cashier
            $cashierUser = User::firstOrCreate(
                ['email' => 'cashier@coffee.test'],
                [
                    'company_id' => $company->id,
                    'role_id' => $cashier->id,
                    'default_branch_id' => $branch->id,
                    'name' => 'Demo Cashier',
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $cashierUser->branches()->syncWithoutDetaching([$branch->id => ['is_default' => true, 'can_access' => true]]);
        });

        $this->call([
            SampleDataSeeder::class,
        ]);
    }
}
