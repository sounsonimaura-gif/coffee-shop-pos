<?php

use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\BranchesController;
use App\Http\Controllers\Admin\CashierShiftsController;
use App\Http\Controllers\Admin\CommissionsController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryOrdersController;
use App\Http\Controllers\Admin\DiningTablesController;
use App\Http\Controllers\Admin\ExpenseCategoriesController;
use App\Http\Controllers\Admin\ExpensesController;
use App\Http\Controllers\Admin\IngredientCategoriesController;
use App\Http\Controllers\Admin\IngredientsController;
use App\Http\Controllers\Admin\KitchenStationsController;
use App\Http\Controllers\Admin\MembershipLevelsController;
use App\Http\Controllers\Admin\MenuAddonsController;
use App\Http\Controllers\Admin\MenuCategoriesController;
use App\Http\Controllers\Admin\MenuItemsController;
use App\Http\Controllers\Admin\MenuOptionsController;
use App\Http\Controllers\Admin\MenuSizesController;
use App\Http\Controllers\Admin\NotificationTemplatesController;
use App\Http\Controllers\Admin\OnlineOrdersController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\PaymentMethodsController;
use App\Http\Controllers\Admin\PayrollsController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\PosCountersController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PromotionsController;
use App\Http\Controllers\Admin\PurchasesController;
use App\Http\Controllers\Admin\RecipesController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SaleInvoicesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffSchedulesController;
use App\Http\Controllers\Admin\StockAdjustmentsController;
use App\Http\Controllers\Admin\StockTransfersController;
use App\Http\Controllers\Admin\SuppliersController;
use App\Http\Controllers\Admin\TableFloorsController;
use App\Http\Controllers\Admin\TableZonesController;
use App\Http\Controllers\Admin\TaxRatesController;
use App\Http\Controllers\Admin\UnitsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\WarehousesController;
use App\Http\Controllers\Admin\WasteRecordsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BranchSwitcherController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

// Locale (always available, even for guests so login page can switch)
Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::post('/branch/switch', [BranchSwitcherController::class, 'switch'])->name('branch.switch');

    Route::get('/', fn () => redirect()->route('admin.dashboard'));

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alias');

        // Profile (any authenticated user)
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // POS
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

        // Orders
        Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
        Route::get('/orders/data', [OrdersController::class, 'data'])->name('orders.data');
        Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
        Route::get('/orders/{id}', [OrdersController::class, 'show'])->name('orders.show');
        Route::delete('/orders/{id}', [OrdersController::class, 'destroy'])->name('orders.destroy');

        // Sale Invoices
        Route::get('/sale-invoices', [SaleInvoicesController::class, 'index'])->name('sale-invoices.index');
        Route::get('/sale-invoices/data', [SaleInvoicesController::class, 'data'])->name('sale-invoices.data');
        Route::get('/sale-invoices/{id}', [SaleInvoicesController::class, 'show'])->name('sale-invoices.show');

        // Cashier shifts
        Route::get('/cashier-shifts', [CashierShiftsController::class, 'index'])->name('cashier-shifts.index');
        Route::get('/cashier-shifts/data', [CashierShiftsController::class, 'data'])->name('cashier-shifts.data');
        Route::post('/cashier-shifts/open', [CashierShiftsController::class, 'open'])->name('cashier-shifts.open');
        Route::post('/cashier-shifts/{id}/close', [CashierShiftsController::class, 'close'])->name('cashier-shifts.close');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Audit logs
        Route::get('/audit-logs', [AuditLogsController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/data', [AuditLogsController::class, 'data'])->name('audit-logs.data');

        // Permissions (read-only)
        Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/data', [PermissionsController::class, 'data'])->name('permissions.data');

        // Reports
        Route::get('/reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/data', [ReportsController::class, 'salesData'])->name('reports.sales.data');
        Route::get('/reports/inventory', [ReportsController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/inventory/data', [ReportsController::class, 'inventoryData'])->name('reports.inventory.data');
        Route::get('/reports/expenses', [ReportsController::class, 'expenses'])->name('reports.expenses');
        Route::get('/reports/expenses/data', [ReportsController::class, 'expensesData'])->name('reports.expenses.data');

        // Purchases (full CRUD with line items)
        Route::get('/purchases', [PurchasesController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/data', [PurchasesController::class, 'data'])->name('purchases.data');
        Route::get('/purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchasesController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{id}', [PurchasesController::class, 'show'])->name('purchases.show');
        Route::get('/purchases/{id}/edit', [PurchasesController::class, 'edit'])->name('purchases.edit');
        Route::put('/purchases/{id}', [PurchasesController::class, 'update'])->name('purchases.update');
        Route::delete('/purchases/{id}', [PurchasesController::class, 'destroy'])->name('purchases.destroy');

        // Stock Transfers (full CRUD with line items)
        Route::get('/stock-transfers', [StockTransfersController::class, 'index'])->name('stock-transfers.index');
        Route::get('/stock-transfers/data', [StockTransfersController::class, 'data'])->name('stock-transfers.data');
        Route::get('/stock-transfers/create', [StockTransfersController::class, 'create'])->name('stock-transfers.create');
        Route::post('/stock-transfers', [StockTransfersController::class, 'store'])->name('stock-transfers.store');
        Route::get('/stock-transfers/{id}', [StockTransfersController::class, 'show'])->name('stock-transfers.show');
        Route::get('/stock-transfers/{id}/edit', [StockTransfersController::class, 'edit'])->name('stock-transfers.edit');
        Route::put('/stock-transfers/{id}', [StockTransfersController::class, 'update'])->name('stock-transfers.update');
        Route::delete('/stock-transfers/{id}', [StockTransfersController::class, 'destroy'])->name('stock-transfers.destroy');

        // Stock Adjustments (flat CRUD)
        Route::get('/stock-adjustments', [StockAdjustmentsController::class, 'index'])->name('stock-adjustments.index');
        Route::get('/stock-adjustments/data', [StockAdjustmentsController::class, 'data'])->name('stock-adjustments.data');
        Route::get('/stock-adjustments/create', [StockAdjustmentsController::class, 'create'])->name('stock-adjustments.create');
        Route::post('/stock-adjustments', [StockAdjustmentsController::class, 'store'])->name('stock-adjustments.store');
        Route::get('/stock-adjustments/{id}/edit', [StockAdjustmentsController::class, 'edit'])->name('stock-adjustments.edit');
        Route::put('/stock-adjustments/{id}', [StockAdjustmentsController::class, 'update'])->name('stock-adjustments.update');
        Route::delete('/stock-adjustments/{id}', [StockAdjustmentsController::class, 'destroy'])->name('stock-adjustments.destroy');

        // Waste Records (flat CRUD)
        Route::get('/waste-records', [WasteRecordsController::class, 'index'])->name('waste-records.index');
        Route::get('/waste-records/data', [WasteRecordsController::class, 'data'])->name('waste-records.data');
        Route::get('/waste-records/create', [WasteRecordsController::class, 'create'])->name('waste-records.create');
        Route::post('/waste-records', [WasteRecordsController::class, 'store'])->name('waste-records.store');
        Route::get('/waste-records/{id}/edit', [WasteRecordsController::class, 'edit'])->name('waste-records.edit');
        Route::put('/waste-records/{id}', [WasteRecordsController::class, 'update'])->name('waste-records.update');
        Route::delete('/waste-records/{id}', [WasteRecordsController::class, 'destroy'])->name('waste-records.destroy');

        // Recipes (full CRUD with ingredients)
        Route::get('/recipes', [RecipesController::class, 'index'])->name('recipes.index');
        Route::get('/recipes/data', [RecipesController::class, 'data'])->name('recipes.data');
        Route::get('/recipes/create', [RecipesController::class, 'create'])->name('recipes.create');
        Route::post('/recipes', [RecipesController::class, 'store'])->name('recipes.store');
        Route::get('/recipes/{id}/edit', [RecipesController::class, 'edit'])->name('recipes.edit');
        Route::put('/recipes/{id}', [RecipesController::class, 'update'])->name('recipes.update');
        Route::delete('/recipes/{id}', [RecipesController::class, 'destroy'])->name('recipes.destroy');

        // Generated simple CRUDs (companies, branches, users, roles, menu, etc.)
        foreach ([
            'companies' => CompaniesController::class,
            'branches' => BranchesController::class,
            'warehouses' => WarehousesController::class,
            'pos-counters' => PosCountersController::class,
            'kitchen-stations' => KitchenStationsController::class,
            'table-floors' => TableFloorsController::class,
            'table-zones' => TableZonesController::class,
            'dining-tables' => DiningTablesController::class,
            'menu-categories' => MenuCategoriesController::class,
            'menu-items' => MenuItemsController::class,
            'menu-sizes' => MenuSizesController::class,
            'menu-options' => MenuOptionsController::class,
            'menu-addons' => MenuAddonsController::class,
            'ingredient-categories' => IngredientCategoriesController::class,
            'ingredients' => IngredientsController::class,
            'units' => UnitsController::class,
            'suppliers' => SuppliersController::class,
            'customers' => CustomersController::class,
            'membership-levels' => MembershipLevelsController::class,
            'promotions' => PromotionsController::class,
            'coupons' => CouponsController::class,
            'payment-methods' => PaymentMethodsController::class,
            'tax-rates' => TaxRatesController::class,
            'expense-categories' => ExpenseCategoriesController::class,
            'expenses' => ExpensesController::class,
            'users' => UsersController::class,
            'roles' => RolesController::class,
            'staff' => StaffController::class,
            'staff-schedules' => StaffSchedulesController::class,
            'payrolls' => PayrollsController::class,
            'commissions' => CommissionsController::class,
            'notification-templates' => NotificationTemplatesController::class,
            'online-orders' => OnlineOrdersController::class,
            'delivery-orders' => DeliveryOrdersController::class,
        ] as $slug => $controller) {
            Route::get("/{$slug}", [$controller, 'index'])->name("{$slug}.index");
            Route::get("/{$slug}/data", [$controller, 'data'])->name("{$slug}.data");
            Route::get("/{$slug}/create", [$controller, 'create'])->name("{$slug}.create");
            Route::post("/{$slug}", [$controller, 'store'])->name("{$slug}.store");
            Route::get("/{$slug}/{id}/edit", [$controller, 'edit'])->name("{$slug}.edit");
            Route::put("/{$slug}/{id}", [$controller, 'update'])->name("{$slug}.update");
            Route::delete("/{$slug}/{id}", [$controller, 'destroy'])->name("{$slug}.destroy");
        }
    });
});
