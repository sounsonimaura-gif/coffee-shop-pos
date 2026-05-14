<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';

const page = usePage();
const { t } = useI18n();

const perms = computed(() => page.props.auth?.user?.permissions || []);

function hasAny(...required) {
    if (perms.value.includes('*')) return true;
    return required.some((p) => perms.value.includes(p));
}

const menu = computed(() => [
    {
        label: t('dashboard'),
        icon: 'bi-house-door',
        route: 'admin.dashboard',
        visible: true,
    },
    {
        label: t('pos'),
        icon: 'bi-shop-window',
        visible: hasAny('pos.use', 'orders.view', 'sale_invoices.view'),
        children: [
            { label: t('pos'), route: 'admin.pos.index', visible: hasAny('pos.use') },
            { label: t('orders'), route: 'admin.orders.index', visible: hasAny('orders.view') },
            { label: t('sale_invoices'), route: 'admin.sale-invoices.index', visible: hasAny('sale_invoices.view') },
            { label: t('cashier_shifts'), route: 'admin.cashier-shifts.index', visible: hasAny('cashier_shifts.view') },
        ],
    },
    {
        label: t('menu_items'),
        icon: 'bi-grid',
        visible: hasAny('menu_items.view', 'menu_categories.view'),
        children: [
            { label: t('menu_categories'), route: 'admin.menu-categories.index', visible: hasAny('menu_categories.view') },
            { label: t('menu_items'), route: 'admin.menu-items.index', visible: hasAny('menu_items.view') },
            { label: t('menu_sizes'), route: 'admin.menu-sizes.index', visible: hasAny('menu_sizes.view') },
            { label: t('menu_options'), route: 'admin.menu-options.index', visible: hasAny('menu_options.view') },
            { label: t('menu_addons'), route: 'admin.menu-addons.index', visible: hasAny('menu_addons.view') },
        ],
    },
    {
        label: t('stock'),
        icon: 'bi-box-seam',
        visible: hasAny('ingredients.view', 'purchases.view'),
        children: [
            { label: t('ingredients'), route: 'admin.ingredients.index', visible: hasAny('ingredients.view') },
            { label: t('units'), route: 'admin.units.index', visible: hasAny('units.view') },
            { label: t('suppliers'), route: 'admin.suppliers.index', visible: hasAny('suppliers.view') },
            { label: t('purchases'), route: 'admin.purchases.index', visible: hasAny('purchases.view') },
            { label: t('stock_transfers'), route: 'admin.stock-transfers.index', visible: hasAny('stock_transfers.view') },
            { label: t('stock_adjustments'), route: 'admin.stock-adjustments.index', visible: hasAny('stock_adjustments.view') },
            { label: t('waste_records'), route: 'admin.waste-records.index', visible: hasAny('waste_records.view') },
            { label: t('recipes'), route: 'admin.recipes.index', visible: hasAny('recipes.view') },
        ],
    },
    {
        label: t('hr'),
        icon: 'bi-person-workspace',
        visible: hasAny('staff.view', 'payrolls.view', 'commissions.view'),
        children: [
            { label: t('staff'), route: 'admin.staff.index', visible: hasAny('staff.view') },
            { label: t('staff_schedules'), route: 'admin.staff-schedules.index', visible: hasAny('staff_schedules.view') },
            { label: t('payrolls'), route: 'admin.payrolls.index', visible: hasAny('payrolls.view') },
            { label: t('commissions'), route: 'admin.commissions.index', visible: hasAny('commissions.view') },
        ],
    },
    {
        label: t('online_orders_module'),
        icon: 'bi-truck',
        visible: hasAny('online_orders.view', 'delivery_orders.view'),
        children: [
            { label: t('online_orders'), route: 'admin.online-orders.index', visible: hasAny('online_orders.view') },
            { label: t('delivery_orders'), route: 'admin.delivery-orders.index', visible: hasAny('delivery_orders.view') },
        ],
    },
    {
        label: t('customers'),
        icon: 'bi-people',
        visible: hasAny('customers.view', 'promotions.view'),
        children: [
            { label: t('customers'), route: 'admin.customers.index', visible: hasAny('customers.view') },
            { label: t('membership_levels'), route: 'admin.membership-levels.index', visible: hasAny('membership_levels.view') },
            { label: t('promotions'), route: 'admin.promotions.index', visible: hasAny('promotions.view') },
            { label: t('coupons'), route: 'admin.coupons.index', visible: hasAny('coupons.view') },
        ],
    },
    {
        label: t('branches'),
        icon: 'bi-building',
        visible: hasAny('companies.view', 'branches.view'),
        children: [
            { label: t('companies'), route: 'admin.companies.index', visible: hasAny('companies.view') },
            { label: t('branches'), route: 'admin.branches.index', visible: hasAny('branches.view') },
            { label: t('warehouses'), route: 'admin.warehouses.index', visible: hasAny('warehouses.view') },
            { label: t('pos_counters'), route: 'admin.pos-counters.index', visible: hasAny('pos_counters.view') },
            { label: t('kitchen_stations'), route: 'admin.kitchen-stations.index', visible: hasAny('kitchen_stations.view') },
            { label: t('table_floors'), route: 'admin.table-floors.index', visible: hasAny('table_floors.view') },
            { label: t('table_zones'), route: 'admin.table-zones.index', visible: hasAny('table_zones.view') },
            { label: t('dining_tables'), route: 'admin.dining-tables.index', visible: hasAny('dining_tables.view') },
        ],
    },
    {
        label: t('users'),
        icon: 'bi-person-badge',
        visible: hasAny('users.view', 'roles.view'),
        children: [
            { label: t('users'), route: 'admin.users.index', visible: hasAny('users.view') },
            { label: t('roles'), route: 'admin.roles.index', visible: hasAny('roles.view') },
            { label: t('permissions'), route: 'admin.permissions.index', visible: hasAny('permissions.view') },
        ],
    },
    {
        label: t('expenses'),
        icon: 'bi-cash-stack',
        visible: hasAny('expenses.view'),
        children: [
            { label: t('expense_categories'), route: 'admin.expense-categories.index', visible: hasAny('expense_categories.view') },
            { label: t('expenses'), route: 'admin.expenses.index', visible: hasAny('expenses.view') },
        ],
    },
    {
        label: t('reports'),
        icon: 'bi-bar-chart',
        visible: hasAny('reports.sales', 'reports.inventory', 'reports.expenses'),
        children: [
            { label: t('sale_invoices'), route: 'admin.reports.sales', visible: hasAny('reports.sales') },
            { label: t('stock'), route: 'admin.reports.inventory', visible: hasAny('reports.inventory') },
            { label: t('expenses'), route: 'admin.reports.expenses', visible: hasAny('reports.expenses') },
        ],
    },
    {
        label: t('settings_module'),
        icon: 'bi-gear',
        visible: hasAny('payment_methods.view', 'tax_rates.view', 'system_settings.view'),
        children: [
            { label: t('payment_methods'), route: 'admin.payment-methods.index', visible: hasAny('payment_methods.view') },
            { label: t('tax_rates'), route: 'admin.tax-rates.index', visible: hasAny('tax_rates.view') },
            { label: t('system_settings'), route: 'admin.settings.index', visible: hasAny('system_settings.view') },
            { label: t('notification_templates'), route: 'admin.notification-templates.index', visible: hasAny('notification_templates.view') },
            { label: t('audit_logs'), route: 'admin.audit-logs.index', visible: hasAny('audit_logs.view') },
        ],
    },
]);

function isActive(routeName) {
    if (!routeName) return false;
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
}

function isParentActive(item) {
    if (!item.children) return false;
    return item.children.some((c) => isActive(c.route));
}
</script>

<template>
    <aside class="sidebar-wrapper" data-simplebar="true">
        <div class="sidebar-header">
            <div>
                <img src="/assets/backend/assets/images/logo-icon.png" class="logo-icon" alt="logo icon" onerror="this.style.display='none'" />
            </div>
            <div>
                <h4 class="logo-text">{{ t('app_name') }}</h4>
            </div>
            <div class="toggle-icon ms-auto">
                <i class="bi bi-chevron-double-left"></i>
            </div>
        </div>

        <ul class="metismenu" id="menu">
            <template v-for="(item, i) in menu" :key="i">
                <li v-if="item.visible" :class="{ 'mm-active': isActive(item.route) || isParentActive(item) }">
                    <Link
                        v-if="item.route && !item.children"
                        :href="route(item.route)"
                        :class="{ active: isActive(item.route) }"
                    >
                        <div class="parent-icon"><i :class="['bi', item.icon]"></i></div>
                        <div class="menu-title">{{ item.label }}</div>
                    </Link>
                    <a v-else href="javascript:;" :class="['has-arrow', { 'mm-active': isParentActive(item) }]">
                        <div class="parent-icon"><i :class="['bi', item.icon]"></i></div>
                        <div class="menu-title">{{ item.label }}</div>
                    </a>
                    <ul v-if="item.children" :class="{ 'mm-show': isParentActive(item) }">
                        <template v-for="(child, j) in item.children" :key="j">
                            <li v-if="child.visible">
                                <Link
                                    :href="route(child.route)"
                                    :class="{ active: isActive(child.route) }"
                                >
                                    <i class="bi bi-arrow-right-short"></i>{{ child.label }}
                                </Link>
                            </li>
                        </template>
                    </ul>
                </li>
            </template>
        </ul>
    </aside>
</template>
