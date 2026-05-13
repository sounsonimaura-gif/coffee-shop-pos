<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\ExpenseCategory;
use App\Models\IngredientCategory;
use App\Models\MembershipLevel;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use App\Models\TableFloor;
use App\Models\TableZone;
use App\Models\TaxRate;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $branch = Branch::where('company_id', $company->id)->first();

        if (! $company || ! $branch) {
            return;
        }

        // Units (company-scoped)
        $unitData = [
            ['name' => 'Piece', 'symbol' => 'pc', 'base_unit_multiplier' => 1],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'base_unit_multiplier' => 1000],
            ['name' => 'Gram', 'symbol' => 'g', 'base_unit_multiplier' => 1],
            ['name' => 'Liter', 'symbol' => 'L', 'base_unit_multiplier' => 1000],
            ['name' => 'Milliliter', 'symbol' => 'ml', 'base_unit_multiplier' => 1],
            ['name' => 'Cup', 'symbol' => 'cup', 'base_unit_multiplier' => 1],
        ];
        foreach ($unitData as $u) {
            Unit::firstOrCreate(
                ['company_id' => $company->id, 'name' => $u['name']],
                array_merge($u, ['is_active' => true])
            );
        }

        // Payment Methods
        $methods = [
            ['code' => 'CASH', 'name' => 'Cash', 'type' => 'cash', 'is_default' => true],
            ['code' => 'KHQR', 'name' => 'KHQR', 'type' => 'qr'],
            ['code' => 'CARD', 'name' => 'Card', 'type' => 'card'],
            ['code' => 'BAKONG', 'name' => 'Bakong', 'type' => 'wallet'],
            ['code' => 'WING', 'name' => 'Wing', 'type' => 'wallet'],
        ];
        foreach ($methods as $m) {
            PaymentMethod::firstOrCreate(
                ['company_id' => $company->id, 'code' => $m['code']],
                array_merge($m, ['is_active' => true, 'is_default' => $m['is_default'] ?? false])
            );
        }

        // Tax rates
        TaxRate::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'VAT 10%'],
            ['rate' => 10.0000, 'is_default' => true, 'is_active' => true]
        );
        TaxRate::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Zero Rated'],
            ['rate' => 0.0000, 'is_default' => false, 'is_active' => true]
        );

        // Membership levels
        $levels = [
            ['name' => 'Bronze', 'min_points' => 0, 'discount_percent' => 0, 'point_multiplier' => 1],
            ['name' => 'Silver', 'min_points' => 500, 'discount_percent' => 5, 'point_multiplier' => 1.5],
            ['name' => 'Gold', 'min_points' => 2000, 'discount_percent' => 10, 'point_multiplier' => 2],
        ];
        foreach ($levels as $i => $l) {
            MembershipLevel::firstOrCreate(
                ['company_id' => $company->id, 'name' => $l['name']],
                array_merge($l, ['min_spend' => 0, 'sort_order' => $i, 'is_active' => true])
            );
        }

        // Menu categories
        $categories = ['Coffee', 'Tea', 'Smoothies', 'Bakery', 'Sandwiches', 'Desserts', 'Other'];
        foreach ($categories as $i => $cat) {
            MenuCategory::firstOrCreate(
                ['company_id' => $company->id, 'name' => $cat],
                ['slug' => Str::slug($cat), 'sort_order' => $i, 'show_on_pos' => true, 'status' => 'active']
            );
        }

        $coffeeCat = MenuCategory::where('name', 'Coffee')->first();
        $teaCat = MenuCategory::where('name', 'Tea')->first();

        // Menu items
        $items = [
            ['name' => 'Espresso', 'cat' => $coffeeCat, 'price' => 1.50],
            ['name' => 'Americano', 'cat' => $coffeeCat, 'price' => 2.00],
            ['name' => 'Latte', 'cat' => $coffeeCat, 'price' => 2.50],
            ['name' => 'Cappuccino', 'cat' => $coffeeCat, 'price' => 2.75],
            ['name' => 'Mocha', 'cat' => $coffeeCat, 'price' => 3.00],
            ['name' => 'Iced Coffee', 'cat' => $coffeeCat, 'price' => 2.25],
            ['name' => 'Hot Tea', 'cat' => $teaCat, 'price' => 1.75],
            ['name' => 'Iced Tea', 'cat' => $teaCat, 'price' => 2.00],
            ['name' => 'Matcha Latte', 'cat' => $teaCat, 'price' => 3.00],
        ];
        $i = 1;
        foreach ($items as $it) {
            MenuItem::firstOrCreate(
                ['company_id' => $company->id, 'menu_code' => 'MI-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                [
                    'category_id' => $it['cat']->id,
                    'name' => $it['name'],
                    'slug' => Str::slug($it['name']),
                    'base_price' => $it['price'],
                    'cost_price' => $it['price'] * 0.4,
                    'sale_price' => $it['price'],
                    'preparation_time_minutes' => 5,
                    'track_recipe_stock' => false,
                    'is_featured' => false,
                    'is_best_seller' => false,
                    'availability_status' => 'available',
                    'status' => 'active',
                ]
            );
            $i++;
        }

        // Ingredient categories
        foreach (['Coffee Beans', 'Dairy', 'Syrups', 'Tea Leaves'] as $i => $name) {
            IngredientCategory::firstOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                ['sort_order' => $i, 'is_active' => true]
            );
        }

        // Supplier
        Supplier::firstOrCreate(
            ['company_id' => $company->id, 'supplier_code' => 'SUP-001'],
            [
                'name' => 'Coffee Bean Co.',
                'phone' => '+855 12 333 333',
                'email' => 'beans@supplier.test',
                'contact_person' => 'Sok Phea',
                'status' => 'active',
            ]
        );

        // Floors / Zones
        $floor = TableFloor::firstOrCreate(
            ['branch_id' => $branch->id, 'name' => 'Ground Floor'],
            ['sort_order' => 1, 'is_active' => true]
        );
        TableZone::firstOrCreate(
            ['branch_id' => $branch->id, 'floor_id' => $floor->id, 'name' => 'Main Area'],
            ['sort_order' => 1, 'is_active' => true]
        );

        // Expense categories
        foreach (['Rent', 'Utilities', 'Salaries', 'Marketing', 'Maintenance', 'Other'] as $c) {
            ExpenseCategory::firstOrCreate(
                ['company_id' => $company->id, 'name' => $c],
                ['is_active' => true]
            );
        }
    }
}
