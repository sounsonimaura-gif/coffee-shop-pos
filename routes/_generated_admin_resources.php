<?php

use App\Http\Controllers\Admin\BranchesController;
use App\Http\Controllers\Admin\CompaniesController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\CustomersController;
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
use App\Http\Controllers\Admin\PaymentMethodsController;
use App\Http\Controllers\Admin\PosCountersController;
use App\Http\Controllers\Admin\PromotionsController;
use App\Http\Controllers\Admin\SuppliersController;
use App\Http\Controllers\Admin\TableFloorsController;
use App\Http\Controllers\Admin\TableZonesController;
use App\Http\Controllers\Admin\TaxRatesController;
use App\Http\Controllers\Admin\UnitsController;
use App\Http\Controllers\Admin\WarehousesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('companies', [CompaniesController::class, 'index'])->name('companies.index');
    Route::get('companies/data', [CompaniesController::class, 'data'])->name('companies.data');
    Route::get('companies/create', [CompaniesController::class, 'create'])->name('companies.create');
    Route::post('companies', [CompaniesController::class, 'store'])->name('companies.store');
    Route::get('companies/{id}/edit', [CompaniesController::class, 'edit'])->name('companies.edit');
    Route::put('companies/{id}', [CompaniesController::class, 'update'])->name('companies.update');
    Route::delete('companies/{id}', [CompaniesController::class, 'destroy'])->name('companies.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('branches', [BranchesController::class, 'index'])->name('branches.index');
    Route::get('branches/data', [BranchesController::class, 'data'])->name('branches.data');
    Route::get('branches/create', [BranchesController::class, 'create'])->name('branches.create');
    Route::post('branches', [BranchesController::class, 'store'])->name('branches.store');
    Route::get('branches/{id}/edit', [BranchesController::class, 'edit'])->name('branches.edit');
    Route::put('branches/{id}', [BranchesController::class, 'update'])->name('branches.update');
    Route::delete('branches/{id}', [BranchesController::class, 'destroy'])->name('branches.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('warehouses', [WarehousesController::class, 'index'])->name('warehouses.index');
    Route::get('warehouses/data', [WarehousesController::class, 'data'])->name('warehouses.data');
    Route::get('warehouses/create', [WarehousesController::class, 'create'])->name('warehouses.create');
    Route::post('warehouses', [WarehousesController::class, 'store'])->name('warehouses.store');
    Route::get('warehouses/{id}/edit', [WarehousesController::class, 'edit'])->name('warehouses.edit');
    Route::put('warehouses/{id}', [WarehousesController::class, 'update'])->name('warehouses.update');
    Route::delete('warehouses/{id}', [WarehousesController::class, 'destroy'])->name('warehouses.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('pos-counters', [PosCountersController::class, 'index'])->name('pos-counters.index');
    Route::get('pos-counters/data', [PosCountersController::class, 'data'])->name('pos-counters.data');
    Route::get('pos-counters/create', [PosCountersController::class, 'create'])->name('pos-counters.create');
    Route::post('pos-counters', [PosCountersController::class, 'store'])->name('pos-counters.store');
    Route::get('pos-counters/{id}/edit', [PosCountersController::class, 'edit'])->name('pos-counters.edit');
    Route::put('pos-counters/{id}', [PosCountersController::class, 'update'])->name('pos-counters.update');
    Route::delete('pos-counters/{id}', [PosCountersController::class, 'destroy'])->name('pos-counters.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('kitchen-stations', [KitchenStationsController::class, 'index'])->name('kitchen-stations.index');
    Route::get('kitchen-stations/data', [KitchenStationsController::class, 'data'])->name('kitchen-stations.data');
    Route::get('kitchen-stations/create', [KitchenStationsController::class, 'create'])->name('kitchen-stations.create');
    Route::post('kitchen-stations', [KitchenStationsController::class, 'store'])->name('kitchen-stations.store');
    Route::get('kitchen-stations/{id}/edit', [KitchenStationsController::class, 'edit'])->name('kitchen-stations.edit');
    Route::put('kitchen-stations/{id}', [KitchenStationsController::class, 'update'])->name('kitchen-stations.update');
    Route::delete('kitchen-stations/{id}', [KitchenStationsController::class, 'destroy'])->name('kitchen-stations.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('table-floors', [TableFloorsController::class, 'index'])->name('table-floors.index');
    Route::get('table-floors/data', [TableFloorsController::class, 'data'])->name('table-floors.data');
    Route::get('table-floors/create', [TableFloorsController::class, 'create'])->name('table-floors.create');
    Route::post('table-floors', [TableFloorsController::class, 'store'])->name('table-floors.store');
    Route::get('table-floors/{id}/edit', [TableFloorsController::class, 'edit'])->name('table-floors.edit');
    Route::put('table-floors/{id}', [TableFloorsController::class, 'update'])->name('table-floors.update');
    Route::delete('table-floors/{id}', [TableFloorsController::class, 'destroy'])->name('table-floors.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('table-zones', [TableZonesController::class, 'index'])->name('table-zones.index');
    Route::get('table-zones/data', [TableZonesController::class, 'data'])->name('table-zones.data');
    Route::get('table-zones/create', [TableZonesController::class, 'create'])->name('table-zones.create');
    Route::post('table-zones', [TableZonesController::class, 'store'])->name('table-zones.store');
    Route::get('table-zones/{id}/edit', [TableZonesController::class, 'edit'])->name('table-zones.edit');
    Route::put('table-zones/{id}', [TableZonesController::class, 'update'])->name('table-zones.update');
    Route::delete('table-zones/{id}', [TableZonesController::class, 'destroy'])->name('table-zones.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('dining-tables', [DiningTablesController::class, 'index'])->name('dining-tables.index');
    Route::get('dining-tables/data', [DiningTablesController::class, 'data'])->name('dining-tables.data');
    Route::get('dining-tables/create', [DiningTablesController::class, 'create'])->name('dining-tables.create');
    Route::post('dining-tables', [DiningTablesController::class, 'store'])->name('dining-tables.store');
    Route::get('dining-tables/{id}/edit', [DiningTablesController::class, 'edit'])->name('dining-tables.edit');
    Route::put('dining-tables/{id}', [DiningTablesController::class, 'update'])->name('dining-tables.update');
    Route::delete('dining-tables/{id}', [DiningTablesController::class, 'destroy'])->name('dining-tables.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('menu-categories', [MenuCategoriesController::class, 'index'])->name('menu-categories.index');
    Route::get('menu-categories/data', [MenuCategoriesController::class, 'data'])->name('menu-categories.data');
    Route::get('menu-categories/create', [MenuCategoriesController::class, 'create'])->name('menu-categories.create');
    Route::post('menu-categories', [MenuCategoriesController::class, 'store'])->name('menu-categories.store');
    Route::get('menu-categories/{id}/edit', [MenuCategoriesController::class, 'edit'])->name('menu-categories.edit');
    Route::put('menu-categories/{id}', [MenuCategoriesController::class, 'update'])->name('menu-categories.update');
    Route::delete('menu-categories/{id}', [MenuCategoriesController::class, 'destroy'])->name('menu-categories.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('menu-items', [MenuItemsController::class, 'index'])->name('menu-items.index');
    Route::get('menu-items/data', [MenuItemsController::class, 'data'])->name('menu-items.data');
    Route::get('menu-items/create', [MenuItemsController::class, 'create'])->name('menu-items.create');
    Route::post('menu-items', [MenuItemsController::class, 'store'])->name('menu-items.store');
    Route::get('menu-items/{id}/edit', [MenuItemsController::class, 'edit'])->name('menu-items.edit');
    Route::put('menu-items/{id}', [MenuItemsController::class, 'update'])->name('menu-items.update');
    Route::delete('menu-items/{id}', [MenuItemsController::class, 'destroy'])->name('menu-items.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('menu-sizes', [MenuSizesController::class, 'index'])->name('menu-sizes.index');
    Route::get('menu-sizes/data', [MenuSizesController::class, 'data'])->name('menu-sizes.data');
    Route::get('menu-sizes/create', [MenuSizesController::class, 'create'])->name('menu-sizes.create');
    Route::post('menu-sizes', [MenuSizesController::class, 'store'])->name('menu-sizes.store');
    Route::get('menu-sizes/{id}/edit', [MenuSizesController::class, 'edit'])->name('menu-sizes.edit');
    Route::put('menu-sizes/{id}', [MenuSizesController::class, 'update'])->name('menu-sizes.update');
    Route::delete('menu-sizes/{id}', [MenuSizesController::class, 'destroy'])->name('menu-sizes.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('menu-options', [MenuOptionsController::class, 'index'])->name('menu-options.index');
    Route::get('menu-options/data', [MenuOptionsController::class, 'data'])->name('menu-options.data');
    Route::get('menu-options/create', [MenuOptionsController::class, 'create'])->name('menu-options.create');
    Route::post('menu-options', [MenuOptionsController::class, 'store'])->name('menu-options.store');
    Route::get('menu-options/{id}/edit', [MenuOptionsController::class, 'edit'])->name('menu-options.edit');
    Route::put('menu-options/{id}', [MenuOptionsController::class, 'update'])->name('menu-options.update');
    Route::delete('menu-options/{id}', [MenuOptionsController::class, 'destroy'])->name('menu-options.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('menu-addons', [MenuAddonsController::class, 'index'])->name('menu-addons.index');
    Route::get('menu-addons/data', [MenuAddonsController::class, 'data'])->name('menu-addons.data');
    Route::get('menu-addons/create', [MenuAddonsController::class, 'create'])->name('menu-addons.create');
    Route::post('menu-addons', [MenuAddonsController::class, 'store'])->name('menu-addons.store');
    Route::get('menu-addons/{id}/edit', [MenuAddonsController::class, 'edit'])->name('menu-addons.edit');
    Route::put('menu-addons/{id}', [MenuAddonsController::class, 'update'])->name('menu-addons.update');
    Route::delete('menu-addons/{id}', [MenuAddonsController::class, 'destroy'])->name('menu-addons.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('ingredient-categories', [IngredientCategoriesController::class, 'index'])->name('ingredient-categories.index');
    Route::get('ingredient-categories/data', [IngredientCategoriesController::class, 'data'])->name('ingredient-categories.data');
    Route::get('ingredient-categories/create', [IngredientCategoriesController::class, 'create'])->name('ingredient-categories.create');
    Route::post('ingredient-categories', [IngredientCategoriesController::class, 'store'])->name('ingredient-categories.store');
    Route::get('ingredient-categories/{id}/edit', [IngredientCategoriesController::class, 'edit'])->name('ingredient-categories.edit');
    Route::put('ingredient-categories/{id}', [IngredientCategoriesController::class, 'update'])->name('ingredient-categories.update');
    Route::delete('ingredient-categories/{id}', [IngredientCategoriesController::class, 'destroy'])->name('ingredient-categories.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('ingredients', [IngredientsController::class, 'index'])->name('ingredients.index');
    Route::get('ingredients/data', [IngredientsController::class, 'data'])->name('ingredients.data');
    Route::get('ingredients/create', [IngredientsController::class, 'create'])->name('ingredients.create');
    Route::post('ingredients', [IngredientsController::class, 'store'])->name('ingredients.store');
    Route::get('ingredients/{id}/edit', [IngredientsController::class, 'edit'])->name('ingredients.edit');
    Route::put('ingredients/{id}', [IngredientsController::class, 'update'])->name('ingredients.update');
    Route::delete('ingredients/{id}', [IngredientsController::class, 'destroy'])->name('ingredients.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('units', [UnitsController::class, 'index'])->name('units.index');
    Route::get('units/data', [UnitsController::class, 'data'])->name('units.data');
    Route::get('units/create', [UnitsController::class, 'create'])->name('units.create');
    Route::post('units', [UnitsController::class, 'store'])->name('units.store');
    Route::get('units/{id}/edit', [UnitsController::class, 'edit'])->name('units.edit');
    Route::put('units/{id}', [UnitsController::class, 'update'])->name('units.update');
    Route::delete('units/{id}', [UnitsController::class, 'destroy'])->name('units.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/data', [SuppliersController::class, 'data'])->name('suppliers.data');
    Route::get('suppliers/create', [SuppliersController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{id}/edit', [SuppliersController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{id}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('customers/data', [CustomersController::class, 'data'])->name('customers.data');
    Route::get('customers/create', [CustomersController::class, 'create'])->name('customers.create');
    Route::post('customers', [CustomersController::class, 'store'])->name('customers.store');
    Route::get('customers/{id}/edit', [CustomersController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{id}', [CustomersController::class, 'update'])->name('customers.update');
    Route::delete('customers/{id}', [CustomersController::class, 'destroy'])->name('customers.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('membership-levels', [MembershipLevelsController::class, 'index'])->name('membership-levels.index');
    Route::get('membership-levels/data', [MembershipLevelsController::class, 'data'])->name('membership-levels.data');
    Route::get('membership-levels/create', [MembershipLevelsController::class, 'create'])->name('membership-levels.create');
    Route::post('membership-levels', [MembershipLevelsController::class, 'store'])->name('membership-levels.store');
    Route::get('membership-levels/{id}/edit', [MembershipLevelsController::class, 'edit'])->name('membership-levels.edit');
    Route::put('membership-levels/{id}', [MembershipLevelsController::class, 'update'])->name('membership-levels.update');
    Route::delete('membership-levels/{id}', [MembershipLevelsController::class, 'destroy'])->name('membership-levels.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('promotions', [PromotionsController::class, 'index'])->name('promotions.index');
    Route::get('promotions/data', [PromotionsController::class, 'data'])->name('promotions.data');
    Route::get('promotions/create', [PromotionsController::class, 'create'])->name('promotions.create');
    Route::post('promotions', [PromotionsController::class, 'store'])->name('promotions.store');
    Route::get('promotions/{id}/edit', [PromotionsController::class, 'edit'])->name('promotions.edit');
    Route::put('promotions/{id}', [PromotionsController::class, 'update'])->name('promotions.update');
    Route::delete('promotions/{id}', [PromotionsController::class, 'destroy'])->name('promotions.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('coupons', [CouponsController::class, 'index'])->name('coupons.index');
    Route::get('coupons/data', [CouponsController::class, 'data'])->name('coupons.data');
    Route::get('coupons/create', [CouponsController::class, 'create'])->name('coupons.create');
    Route::post('coupons', [CouponsController::class, 'store'])->name('coupons.store');
    Route::get('coupons/{id}/edit', [CouponsController::class, 'edit'])->name('coupons.edit');
    Route::put('coupons/{id}', [CouponsController::class, 'update'])->name('coupons.update');
    Route::delete('coupons/{id}', [CouponsController::class, 'destroy'])->name('coupons.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('payment-methods', [PaymentMethodsController::class, 'index'])->name('payment-methods.index');
    Route::get('payment-methods/data', [PaymentMethodsController::class, 'data'])->name('payment-methods.data');
    Route::get('payment-methods/create', [PaymentMethodsController::class, 'create'])->name('payment-methods.create');
    Route::post('payment-methods', [PaymentMethodsController::class, 'store'])->name('payment-methods.store');
    Route::get('payment-methods/{id}/edit', [PaymentMethodsController::class, 'edit'])->name('payment-methods.edit');
    Route::put('payment-methods/{id}', [PaymentMethodsController::class, 'update'])->name('payment-methods.update');
    Route::delete('payment-methods/{id}', [PaymentMethodsController::class, 'destroy'])->name('payment-methods.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('tax-rates', [TaxRatesController::class, 'index'])->name('tax-rates.index');
    Route::get('tax-rates/data', [TaxRatesController::class, 'data'])->name('tax-rates.data');
    Route::get('tax-rates/create', [TaxRatesController::class, 'create'])->name('tax-rates.create');
    Route::post('tax-rates', [TaxRatesController::class, 'store'])->name('tax-rates.store');
    Route::get('tax-rates/{id}/edit', [TaxRatesController::class, 'edit'])->name('tax-rates.edit');
    Route::put('tax-rates/{id}', [TaxRatesController::class, 'update'])->name('tax-rates.update');
    Route::delete('tax-rates/{id}', [TaxRatesController::class, 'destroy'])->name('tax-rates.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('expense-categories', [ExpenseCategoriesController::class, 'index'])->name('expense-categories.index');
    Route::get('expense-categories/data', [ExpenseCategoriesController::class, 'data'])->name('expense-categories.data');
    Route::get('expense-categories/create', [ExpenseCategoriesController::class, 'create'])->name('expense-categories.create');
    Route::post('expense-categories', [ExpenseCategoriesController::class, 'store'])->name('expense-categories.store');
    Route::get('expense-categories/{id}/edit', [ExpenseCategoriesController::class, 'edit'])->name('expense-categories.edit');
    Route::put('expense-categories/{id}', [ExpenseCategoriesController::class, 'update'])->name('expense-categories.update');
    Route::delete('expense-categories/{id}', [ExpenseCategoriesController::class, 'destroy'])->name('expense-categories.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('expenses/data', [ExpensesController::class, 'data'])->name('expenses.data');
    Route::get('expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{id}/edit', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::put('expenses/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{id}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');
});
