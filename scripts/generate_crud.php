<?php

/**
 * Generate thin CRUD controllers + Vue Index pages + Form pages for the
 * "simple" admin resources. Run once.
 *
 *   php scripts/generate_crud.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->bootstrapWith([
    LoadEnvironmentVariables::class,
    LoadConfiguration::class,
    HandleExceptions::class,
    RegisterFacades::class,
    RegisterProviders::class,
    BootProviders::class,
]);

use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Support\Str;

/**
 * resource_key => [
 *   model         => App\Models\Foo,
 *   table         => foos,
 *   perm          => foos (permission prefix),
 *   route_prefix  => admin.foos,
 *   route_param   => foo,
 *   view_ns       => Foos (Vue Pages/Foos/{Index,Form}.vue),
 *   title_key     => coffee.foos,
 *   columns       => [['data' => 'name', 'title' => 'coffee.name'], ...],
 *   form_fields   => [
 *       ['name' => 'code', 'label' => 'coffee.code', 'type' => 'text', 'required' => true],
 *       ['name' => 'is_active', 'label' => 'coffee.is_active', 'type' => 'switch'],
 *   ],
 * ]
 */
$resources = [
    'companies' => [
        'model' => 'Company',
        'title_key' => 'companies',
        'columns' => [
            ['data' => 'company_code', 'title' => 'company_code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'phone', 'title' => 'phone'],
            ['data' => 'email', 'title' => 'email'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'company_code', 'label' => 'company_code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'owner_name', 'label' => 'owner_name', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'phone', 'type' => 'text'],
            ['name' => 'email', 'label' => 'email', 'type' => 'email'],
            ['name' => 'website', 'label' => 'website', 'type' => 'text'],
            ['name' => 'address', 'label' => 'address', 'type' => 'textarea'],
            ['name' => 'tax_no', 'label' => 'tax_no', 'type' => 'text'],
            ['name' => 'currency_code', 'label' => 'currency_code', 'type' => 'text', 'default' => 'USD'],
            ['name' => 'language_code', 'label' => 'language_code', 'type' => 'select', 'options' => [['value' => 'en', 'text' => 'English'], ['value' => 'kh', 'text' => 'Khmer']], 'default' => 'en'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
        'company_scoped' => false,
    ],
    'branches' => [
        'model' => 'Branch',
        'title_key' => 'branches',
        'columns' => [
            ['data' => 'branch_code', 'title' => 'branch_code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'phone', 'title' => 'phone'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'branch_code', 'label' => 'branch_code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'phone', 'label' => 'phone', 'type' => 'text'],
            ['name' => 'address', 'label' => 'address', 'type' => 'textarea'],
            ['name' => 'open_time', 'label' => 'open_time', 'type' => 'time'],
            ['name' => 'close_time', 'label' => 'close_time', 'type' => 'time'],
            ['name' => 'is_main_branch', 'label' => 'is_main_branch', 'type' => 'switch'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
    ],
    'warehouses' => [
        'model' => 'Warehouse',
        'title_key' => 'warehouses',
        'columns' => [
            ['data' => 'warehouse_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'is_default', 'title' => 'is_active'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select'],
            ['name' => 'warehouse_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'address', 'label' => 'address', 'type' => 'textarea'],
            ['name' => 'is_default', 'label' => 'is_active', 'type' => 'switch'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
    ],
    'pos-counters' => [
        'model' => 'PosCounter', 'title_key' => 'pos_counters',
        'columns' => [
            ['data' => 'counter_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select', 'required' => true],
            ['name' => 'counter_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'printer_name', 'label' => 'printer', 'type' => 'text'],
            ['name' => 'receipt_size', 'label' => 'receipt_size', 'type' => 'select', 'options' => [['value' => '58mm', 'text' => '58mm'], ['value' => '80mm', 'text' => '80mm'], ['value' => 'A4', 'text' => 'A4']], 'default' => '80mm'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
        'company_scoped' => false,
    ],
    'kitchen-stations' => [
        'model' => 'KitchenStation', 'title_key' => 'kitchen_stations',
        'columns' => [
            ['data' => 'station_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'station_type', 'title' => 'type'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select', 'required' => true],
            ['name' => 'station_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'station_type', 'label' => 'type', 'type' => 'select', 'options' => [['value' => 'coffee_bar', 'text' => 'Coffee Bar'], ['value' => 'tea_bar', 'text' => 'Tea Bar'], ['value' => 'bakery', 'text' => 'Bakery'], ['value' => 'kitchen', 'text' => 'Kitchen'], ['value' => 'dessert', 'text' => 'Dessert'], ['value' => 'packing', 'text' => 'Packing'], ['value' => 'other', 'text' => 'Other']], 'default' => 'other'],
            ['name' => 'printer_name', 'label' => 'printer', 'type' => 'text'],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
        'company_scoped' => false,
    ],
    'table-floors' => [
        'model' => 'TableFloor', 'title_key' => 'table_floors',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'sort_order', 'title' => 'sort_order'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
        'company_scoped' => false,
    ],
    'table-zones' => [
        'model' => 'TableZone', 'title_key' => 'table_zones',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'sort_order', 'title' => 'sort_order'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select', 'required' => true],
            ['name' => 'floor_id', 'label' => 'table_floors', 'type' => 'select_remote', 'source' => 'floors'],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
        'company_scoped' => false,
    ],
    'dining-tables' => [
        'model' => 'DiningTable', 'title_key' => 'dining_tables',
        'columns' => [
            ['data' => 'table_no', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'capacity', 'title' => 'quantity'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select', 'required' => true],
            ['name' => 'floor_id', 'label' => 'table_floors', 'type' => 'select_remote', 'source' => 'floors'],
            ['name' => 'zone_id', 'label' => 'table_zones', 'type' => 'select_remote', 'source' => 'zones'],
            ['name' => 'table_no', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text'],
            ['name' => 'capacity', 'label' => 'quantity', 'type' => 'number', 'default' => 4],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'available', 'text' => 'Available'], ['value' => 'occupied', 'text' => 'Occupied'], ['value' => 'reserved', 'text' => 'Reserved'], ['value' => 'closed', 'text' => 'Closed']], 'default' => 'available'],
        ],
        'company_scoped' => false,
    ],
    'menu-categories' => [
        'model' => 'MenuCategory', 'title_key' => 'menu_categories',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'sort_order', 'title' => 'sort_order'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'slug', 'label' => 'slug', 'type' => 'text'],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'show_on_pos', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
    ],
    'menu-items' => [
        'model' => 'MenuItem', 'title_key' => 'menu_items',
        'columns' => [
            ['data' => 'menu_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'sale_price', 'title' => 'price'],
            ['data' => 'availability_status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'category_id', 'label' => 'menu_categories', 'type' => 'select_remote', 'source' => 'menu_categories'],
            ['name' => 'menu_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'description', 'type' => 'textarea'],
            ['name' => 'base_price', 'label' => 'price', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'cost_price', 'label' => 'cost_price', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'sale_price', 'label' => 'price', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'preparation_time_minutes', 'label' => 'prep_time_minutes', 'type' => 'number', 'default' => 0],
            ['name' => 'is_featured', 'label' => 'is_featured', 'type' => 'switch'],
            ['name' => 'is_best_seller', 'label' => 'is_best_seller', 'type' => 'switch'],
            ['name' => 'availability_status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'available', 'text' => 'Available'], ['value' => 'unavailable', 'text' => 'Unavailable'], ['value' => 'sold_out', 'text' => 'Sold out']], 'default' => 'available'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
    ],
    'menu-sizes' => [
        'model' => 'MenuSize', 'title_key' => 'menu_sizes',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'sort_order', 'title' => 'sort_order'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'code', 'label' => 'code', 'type' => 'text'],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'menu-options' => [
        'model' => 'MenuOption', 'title_key' => 'menu_options',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'is_required', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'is_required', 'label' => 'is_active', 'type' => 'switch'],
            ['name' => 'allow_multiple', 'label' => 'is_active', 'type' => 'switch'],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
        ],
    ],
    'menu-addons' => [
        'model' => 'MenuAddon', 'title_key' => 'menu_addons',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'price', 'title' => 'price'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'price', 'label' => 'price', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'ingredient-categories' => [
        'model' => 'IngredientCategory', 'title_key' => 'ingredient_categories',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'ingredients' => [
        'model' => 'Ingredient', 'title_key' => 'ingredients',
        'columns' => [
            ['data' => 'ingredient_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'category_id', 'label' => 'ingredient_categories', 'type' => 'select_remote', 'source' => 'ingredient_categories'],
            ['name' => 'unit_id', 'label' => 'units', 'type' => 'select_remote', 'source' => 'units'],
            ['name' => 'ingredient_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'sku', 'label' => 'code', 'type' => 'text'],
            ['name' => 'min_stock_level', 'label' => 'quantity', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'reorder_level', 'label' => 'quantity', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'cost_per_unit', 'label' => 'cost_price', 'type' => 'number', 'step' => '0.0001', 'default' => 0],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'units' => [
        'model' => 'Unit', 'title_key' => 'units',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'symbol', 'title' => 'code'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'symbol', 'label' => 'code', 'type' => 'text'],
            ['name' => 'base_unit_multiplier', 'label' => 'quantity', 'type' => 'number', 'step' => '0.000001', 'default' => 1],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'suppliers' => [
        'model' => 'Supplier', 'title_key' => 'suppliers',
        'columns' => [
            ['data' => 'supplier_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'phone', 'title' => 'phone'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'supplier_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'contact_person', 'label' => 'contact_person', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'phone', 'type' => 'text'],
            ['name' => 'email', 'label' => 'email', 'type' => 'email'],
            ['name' => 'tax_no', 'label' => 'tax_no', 'type' => 'text'],
            ['name' => 'address', 'label' => 'address', 'type' => 'textarea'],
            ['name' => 'credit_limit', 'label' => 'credit_limit', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'credit_days', 'label' => 'credit_days', 'type' => 'number', 'default' => 0],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
    ],
    'customers' => [
        'model' => 'Customer', 'title_key' => 'customers',
        'columns' => [
            ['data' => 'customer_code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'phone', 'title' => 'phone'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'membership_level_id', 'label' => 'membership_levels', 'type' => 'select_remote', 'source' => 'membership_levels'],
            ['name' => 'customer_code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'phone', 'label' => 'phone', 'type' => 'text'],
            ['name' => 'email', 'label' => 'email', 'type' => 'email'],
            ['name' => 'address', 'label' => 'address', 'type' => 'textarea'],
            ['name' => 'gender', 'label' => 'gender', 'type' => 'select', 'options' => [['value' => 'male', 'text' => 'Male'], ['value' => 'female', 'text' => 'Female'], ['value' => 'other', 'text' => 'Other']]],
            ['name' => 'birthday', 'label' => 'birthday', 'type' => 'date'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'active', 'text' => 'Active'], ['value' => 'inactive', 'text' => 'Inactive']], 'default' => 'active'],
        ],
    ],
    'membership-levels' => [
        'model' => 'MembershipLevel', 'title_key' => 'membership_levels',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'min_points', 'title' => 'quantity'],
            ['data' => 'discount_percent', 'title' => 'discount'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'min_spend', 'label' => 'price', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'min_points', 'label' => 'quantity', 'type' => 'number', 'default' => 0],
            ['name' => 'discount_percent', 'label' => 'discount', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'point_multiplier', 'label' => 'quantity', 'type' => 'number', 'step' => '0.01', 'default' => 1],
            ['name' => 'sort_order', 'label' => 'sort_order', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'promotions' => [
        'model' => 'Promotion', 'title_key' => 'promotions',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'discount_type', 'title' => 'type'],
            ['data' => 'discount_value', 'title' => 'discount'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'promo_code', 'label' => 'code', 'type' => 'text'],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'description', 'type' => 'textarea'],
            ['name' => 'discount_type', 'label' => 'type', 'type' => 'select', 'options' => [['value' => 'percentage', 'text' => 'Percentage'], ['value' => 'fixed', 'text' => 'Fixed Amount']], 'default' => 'percentage'],
            ['name' => 'discount_value', 'label' => 'discount', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'min_purchase_amount', 'label' => 'price', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'start_at', 'label' => 'from', 'type' => 'datetime'],
            ['name' => 'end_at', 'label' => 'to', 'type' => 'datetime'],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'coupons' => [
        'model' => 'Coupon', 'title_key' => 'coupons',
        'columns' => [
            ['data' => 'code', 'title' => 'code'],
            ['data' => 'discount_value', 'title' => 'discount'],
            ['data' => 'usage_count', 'title' => 'quantity'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'description', 'type' => 'textarea'],
            ['name' => 'discount_type', 'label' => 'type', 'type' => 'select', 'options' => [['value' => 'percentage', 'text' => 'Percentage'], ['value' => 'fixed', 'text' => 'Fixed Amount']], 'default' => 'percentage'],
            ['name' => 'discount_value', 'label' => 'discount', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'min_purchase_amount', 'label' => 'price', 'type' => 'number', 'step' => '0.01', 'default' => 0],
            ['name' => 'usage_limit', 'label' => 'quantity', 'type' => 'number'],
            ['name' => 'start_at', 'label' => 'from', 'type' => 'datetime'],
            ['name' => 'end_at', 'label' => 'to', 'type' => 'datetime'],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'payment-methods' => [
        'model' => 'PaymentMethod', 'title_key' => 'payment_methods',
        'columns' => [
            ['data' => 'code', 'title' => 'code'],
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'type', 'title' => 'type'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'code', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'type', 'label' => 'type', 'type' => 'select', 'options' => [['value' => 'cash', 'text' => 'Cash'], ['value' => 'card', 'text' => 'Card'], ['value' => 'bank', 'text' => 'Bank'], ['value' => 'qr', 'text' => 'QR'], ['value' => 'wallet', 'text' => 'Wallet'], ['value' => 'other', 'text' => 'Other']], 'default' => 'cash'],
            ['name' => 'account_no', 'label' => 'code', 'type' => 'text'],
            ['name' => 'is_default', 'label' => 'is_active', 'type' => 'switch'],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'tax-rates' => [
        'model' => 'TaxRate', 'title_key' => 'tax_rates',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'rate', 'title' => 'discount'],
            ['data' => 'is_default', 'title' => 'is_active'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'rate', 'label' => 'discount', 'type' => 'number', 'step' => '0.0001', 'default' => 0],
            ['name' => 'is_default', 'label' => 'is_active', 'type' => 'switch'],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'expense-categories' => [
        'model' => 'ExpenseCategory', 'title_key' => 'expense_categories',
        'columns' => [
            ['data' => 'name', 'title' => 'name'],
            ['data' => 'is_active', 'title' => 'is_active'],
        ],
        'fields' => [
            ['name' => 'name', 'label' => 'name', 'type' => 'text', 'required' => true],
            ['name' => 'is_active', 'label' => 'is_active', 'type' => 'switch', 'default' => true],
        ],
    ],
    'expenses' => [
        'model' => 'Expense', 'title_key' => 'expenses',
        'columns' => [
            ['data' => 'expense_no', 'title' => 'code'],
            ['data' => 'expense_date', 'title' => 'from'],
            ['data' => 'amount', 'title' => 'price'],
            ['data' => 'status', 'title' => 'status'],
        ],
        'fields' => [
            ['name' => 'expense_category_id', 'label' => 'expense_categories', 'type' => 'select_remote', 'source' => 'expense_categories'],
            ['name' => 'payment_method_id', 'label' => 'payment_methods', 'type' => 'select_remote', 'source' => 'payment_methods'],
            ['name' => 'branch_id', 'label' => 'branch', 'type' => 'branch_select'],
            ['name' => 'expense_no', 'label' => 'code', 'type' => 'text', 'required' => true],
            ['name' => 'expense_date', 'label' => 'from', 'type' => 'date', 'required' => true],
            ['name' => 'amount', 'label' => 'price', 'type' => 'number', 'step' => '0.01', 'required' => true],
            ['name' => 'reference_no', 'label' => 'code', 'type' => 'text'],
            ['name' => 'notes', 'label' => 'description', 'type' => 'textarea'],
            ['name' => 'status', 'label' => 'status', 'type' => 'select', 'options' => [['value' => 'pending', 'text' => 'Pending'], ['value' => 'approved', 'text' => 'Approved'], ['value' => 'paid', 'text' => 'Paid']], 'default' => 'pending'],
        ],
    ],
];

// Discover columns for missing fields automatically
foreach ($resources as $key => &$res) {
    $modelClass = 'App\\Models\\'.$res['model'];
    $instance = new $modelClass;
    $table = $instance->getTable();
    $res['table'] = $table;
    $res['perm'] = str_replace('-', '_', $key);
    $res['route_prefix'] = "admin.$key";
    $res['route_param'] = Str::singular(str_replace('-', '_', $key));
    $res['view_ns'] = Str::studly(str_replace('-', '_', $key));
    $res['title_key'] = $res['title_key'] ?? Str::snake($key);
    $res['company_scoped'] = $res['company_scoped'] ?? true;
}
unset($res);

// ---- Generate controllers ----
$controllerDir = __DIR__.'/../app/Http/Controllers/Admin';
@mkdir($controllerDir, 0755, true);

foreach ($resources as $key => $res) {
    $name = $res['view_ns'].'Controller';
    $columnsPhp = var_export(
        array_map(fn ($c) => [
            'data' => $c['data'],
            'title' => 'coffee.'.$c['title'],
        ], $res['columns']),
        true
    );

    $rulesPhp = '';
    foreach ($res['fields'] as $f) {
        $rules = [];
        if (! empty($f['required'])) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }
        switch ($f['type']) {
            case 'email': $rules[] = 'email';
                $rules[] = 'max:255';
                break;
            case 'text': $rules[] = 'string';
                $rules[] = 'max:255';
                break;
            case 'textarea': $rules[] = 'string';
                break;
            case 'number': $rules[] = 'numeric';
                break;
            case 'date': $rules[] = 'date';
                break;
            case 'datetime': $rules[] = 'date';
                break;
            case 'time': $rules[] = 'date_format:H:i,H:i:s';
                break;
            case 'switch': $rules[] = 'boolean';
                break;
            case 'select': case 'select_remote': case 'branch_select':
                $rules[] = 'string';
                break;
        }
        $rulesPhp .= "            '".$f['name']."' => ['".implode("','", $rules)."'],\n";
    }

    $modelClass = $res['model'];
    $companyLine = $res['company_scoped'] ? '' : "\n    protected function baseQuery(): \\Illuminate\\Database\\Eloquent\\Builder\n    {\n        return \\App\\Models\\$modelClass::query();\n    }\n";

    $formPropsBuild = '';
    $sources = [];
    foreach ($res['fields'] as $f) {
        if (($f['type'] ?? '') === 'select_remote') {
            $sources[$f['source']] = true;
        }
    }
    foreach (array_keys($sources) as $src) {
        $srcModel = [
            'menu_categories' => 'MenuCategory',
            'ingredient_categories' => 'IngredientCategory',
            'units' => 'Unit',
            'membership_levels' => 'MembershipLevel',
            'expense_categories' => 'ExpenseCategory',
            'payment_methods' => 'PaymentMethod',
            'floors' => 'TableFloor',
            'zones' => 'TableZone',
        ][$src] ?? Str::studly(Str::singular($src));

        $formPropsBuild .= "            '$src' => \\App\\Models\\$srcModel::query()->select('id', 'name')->orderBy('name')->get(),\n";
    }

    $branchesProp = '';
    foreach ($res['fields'] as $f) {
        if (($f['type'] ?? '') === 'branch_select') {
            $branchesProp = "            'branches' => \\App\\Models\\Branch::query()\n                ->when(request()->user()?->company_id, fn (\$q, \$cid) => \$q->where('company_id', \$cid))\n                ->orderBy('name')->get(['id', 'name']),\n";
            break;
        }
    }

    $controllerCode = <<<PHP
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCrudController;
use App\Models\\$modelClass;
use Illuminate\Http\Request;

class $name extends BaseCrudController
{
    protected string \$model = $modelClass::class;
    protected string \$viewNamespace = '{$res['view_ns']}';
    protected string \$permissionPrefix = '{$res['perm']}';
    protected string \$routePrefix = '{$res['route_prefix']}';
    protected string \$titleKey = 'coffee.{$res['title_key']}';

    protected function columns(): array
    {
        return $columnsPhp;
    }

    protected function rules(Request \$request, \$model = null): array
    {
        return [
$rulesPhp        ];
    }

    protected function formProps(?object \$model = null): array
    {
        return [
$formPropsBuild$branchesProp        ];
    }
$companyLine}
PHP;
    file_put_contents("$controllerDir/$name.php", $controllerCode);
    echo "Controller: $name\n";
}

// ---- Generate Vue Index + Form pages ----
$pagesDir = __DIR__.'/../resources/js/Pages';
@mkdir($pagesDir, 0755, true);

foreach ($resources as $key => $res) {
    $dir = "$pagesDir/{$res['view_ns']}";
    @mkdir($dir, 0755, true);

    // Index page (uses YajraDataTable component)
    $indexVue = <<<'VUE'
<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import YajraDataTable from '@/Components/YajraDataTable.vue';
import confirmDelete from '@/composables/confirmDelete.js';

const props = defineProps({
    titleKey: { type: String, required: true },
    routes: { type: Object, required: true },
    columns: { type: Array, required: true },
    permissions: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const refreshKey = ref(0);

function renderColumns() {
    return [
        ...props.columns.map((c) => ({
            data: c.data,
            name: c.name,
            title: c.title,
            orderable: c.orderable,
            searchable: c.searchable,
            render: (data) => {
                if (data === true || data === 1 || data === '1') return '<span class="badge bg-success">' + t('yes') + '</span>';
                if (data === false || data === 0 || data === '0') return '<span class="badge bg-secondary">' + t('no') + '</span>';
                if (data === null || data === undefined) return '';
                return String(data);
            },
        })),
        {
            data: 'actions',
            name: 'actions',
            title: t('actions'),
            orderable: false,
            searchable: false,
        },
    ];
}

function onClick(e) {
    const btn = e.target.closest('.js-confirm-delete');
    if (btn) {
        e.preventDefault();
        confirmDelete({
            url: btn.dataset.url,
            title: t('are_you_sure'),
            text: t('you_cannot_undo_this'),
            confirmButtonText: t('yes_delete_it'),
            cancelButtonText: t('cancel'),
            successMessage: t('deleted_successfully'),
            onSuccess: () => refreshKey.value++,
        });
        return;
    }
    const link = e.target.closest('a[data-inertia]');
    if (link) {
        e.preventDefault();
        router.visit(link.getAttribute('href'));
    }
}
</script>

<template>
    <AdminLayout :title="t(titleKey.replace('coffee.', ''))" :page-title="t(titleKey.replace('coffee.', ''))">
        <div class="card" @click="onClick">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ t(titleKey.replace('coffee.', '')) }}</h5>
                <Link
                    v-if="permissions.create"
                    :href="route(routes.create)"
                    class="btn btn-primary btn-sm"
                >
                    <i class="bi bi-plus-lg me-1"></i>{{ t('new') }}
                </Link>
            </div>
            <div class="card-body">
                <YajraDataTable
                    :ajax-url="route(routes.data)"
                    :columns="renderColumns()"
                    :refresh-key="refreshKey"
                />
            </div>
        </div>
    </AdminLayout>
</template>
VUE;

    file_put_contents("$dir/Index.vue", $indexVue);

    // Form page
    $fieldsVue = '';
    foreach ($res['fields'] as $f) {
        $name = $f['name'];
        $label = $f['label'];
        $required = ! empty($f['required']) ? 'required' : '';
        switch ($f['type']) {
            case 'switch':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')">
                    <div class="form-check form-switch">
                        <input v-model="form.$name" type="checkbox" class="form-check-input" :id="'switch-$name'" />
                        <label class="form-check-label" :for="'switch-$name'">{{ form.$name ? t('yes') : t('no') }}</label>
                    </div>
                </FormField>
VTL;
                break;
            case 'textarea':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name">
                    <textarea v-model="form.$name" class="form-control" rows="2"></textarea>
                </FormField>
VTL;
                break;
            case 'select':
                $opts = $f['options'] ?? [];
                $optsArr = json_encode($opts);
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name" :required="!!'$required'">
                    <TomSelectField v-model="form.$name" :options='$optsArr' value-field="value" label-field="text" :placeholder="t('select_placeholder')" />
                </FormField>
VTL;
                break;
            case 'select_remote':
                $source = $f['source'];
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name">
                    <TomSelectField v-model="form.$name" :options="props.props.$source || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
VTL;
                break;
            case 'branch_select':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name">
                    <TomSelectField v-model="form.$name" :options="props.props.branches || []" value-field="id" label-field="name" :placeholder="t('select_placeholder')" />
                </FormField>
VTL;
                break;
            case 'date':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name" :required="!!'$required'">
                    <FlatpickrField v-model="form.$name" date-format="Y-m-d" />
                </FormField>
VTL;
                break;
            case 'datetime':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name">
                    <FlatpickrField v-model="form.$name" :enable-time="true" date-format="Y-m-d H:i" />
                </FormField>
VTL;
                break;
            case 'time':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name">
                    <FlatpickrField v-model="form.$name" :time-only="true" />
                </FormField>
VTL;
                break;
            case 'number':
                $step = $f['step'] ?? '1';
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name" :required="!!'$required'">
                    <input v-model="form.$name" type="number" step="$step" class="form-control" />
                </FormField>
VTL;
                break;
            case 'email':
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name" :required="!!'$required'">
                    <input v-model="form.$name" type="email" class="form-control" />
                </FormField>
VTL;
                break;
            default:
                $fieldsVue .= <<<VTL

                <FormField :label="t('$label')" :error="form.errors.$name" :required="!!'$required'">
                    <input v-model="form.$name" type="text" class="form-control" />
                </FormField>
VTL;
        }
    }

    // initial form values
    $initial = [];
    foreach ($res['fields'] as $f) {
        $name = $f['name'];
        $default = $f['default'] ?? null;
        if ($f['type'] === 'switch') {
            $default = $default ?? false;
        }
        if ($default === null) {
            $initial[] = "$name: model.$name ?? null";
        } else {
            $defaultJs = is_bool($default) ? ($default ? 'true' : 'false') : (is_numeric($default) ? $default : "'$default'");
            $initial[] = "$name: model.$name ?? $defaultJs";
        }
    }
    $initialJs = implode(",\n        ", $initial);

    $formVue = <<<VUE
<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormField from '@/Components/FormField.vue';
import TomSelectField from '@/Components/TomSelectField.vue';
import FlatpickrField from '@/Components/FlatpickrField.vue';

const props = defineProps({
    titleKey: { type: String, required: true },
    isEdit: { type: Boolean, default: false },
    model: { type: Object, default: () => ({}) },
    props: { type: Object, default: () => ({}) },
    routes: { type: Object, required: true },
});

const { t } = useI18n();

const model = props.model || {};

const form = useForm({
        $initialJs,
});

function submit() {
    if (props.isEdit) {
        const [name, id] = props.routes.update;
        form.put(route(name, id));
    } else {
        form.post(route(props.routes.store));
    }
}
</script>

<template>
    <AdminLayout
        :title="(isEdit ? t('edit') : t('create')) + ' — ' + t(titleKey.replace('coffee.', ''))"
        :page-title="(isEdit ? t('edit') : t('create')) + ' — ' + t(titleKey.replace('coffee.', ''))"
    >
        <form class="card" @submit.prevent="submit">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ (isEdit ? t('edit') : t('create')) + ' — ' + t(titleKey.replace('coffee.', '')) }}</h5>
                <Link :href="route(routes.index)" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ t('back_to_list') }}
                </Link>
            </div>
            <div class="card-body row">
                <div class="col-md-8">
$fieldsVue
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="bi bi-save me-1"></i>{{ t('save') }}
                </button>
                <Link :href="route(routes.index)" class="btn btn-outline-secondary">{{ t('cancel') }}</Link>
            </div>
        </form>
    </AdminLayout>
</template>
VUE;

    file_put_contents("$dir/Form.vue", $formVue);

    echo "Pages: {$res['view_ns']}\n";
}

// ---- Route stubs file ----
$routesFile = __DIR__.'/../routes/_generated_admin_resources.php';
$routesPhp = "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n";
foreach ($resources as $key => $res) {
    $controllerFqn = 'App\\Http\\Controllers\\Admin\\'.$res['view_ns'].'Controller';
    $routesPhp .= "Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {\n";
    $routesPhp .= "    Route::get('$key', [\\$controllerFqn::class, 'index'])->name('$key.index');\n";
    $routesPhp .= "    Route::get('$key/data', [\\$controllerFqn::class, 'data'])->name('$key.data');\n";
    $routesPhp .= "    Route::get('$key/create', [\\$controllerFqn::class, 'create'])->name('$key.create');\n";
    $routesPhp .= "    Route::post('$key', [\\$controllerFqn::class, 'store'])->name('$key.store');\n";
    $routesPhp .= "    Route::get('$key/{id}/edit', [\\$controllerFqn::class, 'edit'])->name('$key.edit');\n";
    $routesPhp .= "    Route::put('$key/{id}', [\\$controllerFqn::class, 'update'])->name('$key.update');\n";
    $routesPhp .= "    Route::delete('$key/{id}', [\\$controllerFqn::class, 'destroy'])->name('$key.destroy');\n";
    $routesPhp .= "});\n\n";
}
file_put_contents($routesFile, $routesPhp);
echo "\nRoutes written to $routesFile\n";

echo "\nDone.\n";
