{{--
  Legacy sidebar shell (Skodash). For Inertia routes the same structure
  is reproduced in resources/js/Layouts/AdminSidebar.vue so that menu
  text can switch language without a full reload. The named routes
  below feed Ziggy via @routes.
--}}
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('assets/backend/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">{{ __('coffee.app_name') }}</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class="bi bi-chevron-double-left"></i></div>
    </div>

    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class="bi bi-house-door"></i></div>
                <div class="menu-title">{{ __('coffee.dashboard') }}</div>
            </a>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-shop-window"></i></div>
                <div class="menu-title">{{ __('coffee.pos') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.pos.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.pos') }}</a></li>
                <li><a href="{{ route('admin.orders.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.orders') }}</a></li>
                <li><a href="{{ route('admin.sale-invoices.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.sale_invoices') }}</a></li>
                <li><a href="{{ route('admin.cashier-shifts.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.cashier_shifts') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-grid"></i></div>
                <div class="menu-title">{{ __('coffee.menu_items') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.menu-categories.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.menu_categories') }}</a></li>
                <li><a href="{{ route('admin.menu-items.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.menu_items') }}</a></li>
                <li><a href="{{ route('admin.menu-sizes.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.menu_sizes') }}</a></li>
                <li><a href="{{ route('admin.menu-options.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.menu_options') }}</a></li>
                <li><a href="{{ route('admin.menu-addons.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.menu_addons') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-box-seam"></i></div>
                <div class="menu-title">{{ __('coffee.stock') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.ingredients.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.ingredients') }}</a></li>
                <li><a href="{{ route('admin.units.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.units') }}</a></li>
                <li><a href="{{ route('admin.suppliers.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.suppliers') }}</a></li>
                <li><a href="{{ route('admin.purchases.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.purchases') }}</a></li>
                <li><a href="{{ route('admin.stock-transfers.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.stock_transfers') }}</a></li>
                <li><a href="{{ route('admin.stock-adjustments.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.stock_adjustments') }}</a></li>
                <li><a href="{{ route('admin.waste-records.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.waste_records') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-people"></i></div>
                <div class="menu-title">{{ __('coffee.customers') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.customers.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.customers') }}</a></li>
                <li><a href="{{ route('admin.membership-levels.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.membership_levels') }}</a></li>
                <li><a href="{{ route('admin.promotions.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.promotions') }}</a></li>
                <li><a href="{{ route('admin.coupons.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.coupons') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-building"></i></div>
                <div class="menu-title">{{ __('coffee.branches') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.companies.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.companies') }}</a></li>
                <li><a href="{{ route('admin.branches.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.branches') }}</a></li>
                <li><a href="{{ route('admin.warehouses.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.warehouses') }}</a></li>
                <li><a href="{{ route('admin.pos-counters.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.pos_counters') }}</a></li>
                <li><a href="{{ route('admin.kitchen-stations.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.kitchen_stations') }}</a></li>
                <li><a href="{{ route('admin.table-floors.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.table_floors') }}</a></li>
                <li><a href="{{ route('admin.table-zones.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.table_zones') }}</a></li>
                <li><a href="{{ route('admin.dining-tables.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.dining_tables') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-person-badge"></i></div>
                <div class="menu-title">{{ __('coffee.users') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.users.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.users') }}</a></li>
                <li><a href="{{ route('admin.roles.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.roles') }}</a></li>
                <li><a href="{{ route('admin.permissions.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.permissions') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="menu-title">{{ __('coffee.expenses') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.expense-categories.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.expense_categories') }}</a></li>
                <li><a href="{{ route('admin.expenses.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.expenses') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-bar-chart"></i></div>
                <div class="menu-title">{{ __('coffee.reports') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.reports.sales') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.sale_invoices') }}</a></li>
                <li><a href="{{ route('admin.reports.inventory') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.stock') }}</a></li>
                <li><a href="{{ route('admin.reports.expenses') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.expenses') }}</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="bi bi-gear"></i></div>
                <div class="menu-title">{{ __('coffee.settings_module') }}</div>
            </a>
            <ul>
                <li><a href="{{ route('admin.payment-methods.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.payment_methods') }}</a></li>
                <li><a href="{{ route('admin.tax-rates.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.tax_rates') }}</a></li>
                <li><a href="{{ route('admin.settings.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.system_settings') }}</a></li>
                <li><a href="{{ route('admin.audit-logs.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('coffee.audit_logs') }}</a></li>
            </ul>
        </li>
    </ul>
</aside>
