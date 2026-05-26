# មេរៀនប្រើប្រាស់ប្រព័ន្ធ — Coffee Shop & POS Management System
# (Training Manual)

> **ជំពូកទី ១ — ទិដ្ឋភាពទូទៅ (Overview)**

---

## ១.១ អ្វីជា Project នេះ?

**Coffee Shop & POS Management System** គឺជា Web Application សាងសង់ឡើងលើ **Laravel 12 + Vue 3 + Inertia.js** សម្រាប់គ្រប់គ្រងប្រតិបត្តិការហាងកាហ្វេ / ភោជនីយដ្ឋានពេញលេញ ចាប់ពី**ការគ្រប់គ្រងបញ្ជី (Stock)**, **ការទិញ (Purchases)**, **ការលក់ (POS)**, **ការវាយតម្លៃរូបមន្ត (Recipes)**, **ការគ្រប់គ្រងបុគ្គលិក (HR)**, រហូតដល់**ការបញ្ជាទិញតាមអ៊ីនធឺណិត (Online & Delivery Orders)**។

ប្រព័ន្ធនេះគាំទ្រ **Multi-Company / Multi-Branch** (ក្រុមហ៊ុនច្រើន សាខាច្រើន) ដោយប្រើ `company_id` និង active-branch context កំណត់នៅក្នុង session ហើយ share ឡើង Vue តាមរយៈ Inertia shared props។

| លក្ខណៈ | ការពិពណ៌នា |
|---|---|
| Multi-tenant | Data scoped by `company_id` + active branch |
| RBAC | Manual Roles + Permissions (មិនប្រើ Spatie package) |
| i18n | Khmer / English switching មិន reload page |
| POS Flow | Cashier Shift → Orders → Sale Invoices → Payments |
| Stock Ledger | Centralized `StockService` — atomic writes ទៅ `stock_movements`, `stock_balances`, `stock_batches` |
| Auto-calc | Recipe cost roll-up, Payroll net salary, Commission amount |

## ១.២ បច្ចេកវិទ្យាដែលប្រើ (Tech Stack)

| ផ្នែក | បច្ចេកវិទ្យា |
|---|---|
| Backend Framework | **Laravel 12** (PHP 8.2+) |
| Frontend Framework | **Vue 3** + **Inertia.js** |
| Server-side Routes in JS | **Ziggy** |
| Build Tool | **Vite** |
| CSS Framework | **Bootstrap 5** (custom admin SCSS, no Skodash theme) |
| DataTables | **Yajra DataTables** server-side processing (loaded from CDN as classic scripts — see §១០.១) |
| Select Inputs | **Tom Select** |
| Date/Time Pickers | **flatpickr** |
| Confirm Dialogs | **SweetAlert2** |
| Toast Notifications | **PHP-Flasher** |
| Database | **MySQL 8** ឬ **SQLite** (default for local/CI) |
| Pagination | Fixed Bootstrap 5 rounded pagination (centered, no jump) |
| Auth | Custom session-based login (no Breeze / Jetstream) |

## ១.៣ រចនាសម្ព័ន្ធ Project (Folder Structure)

```
coffee-shop-pos/
├── app/
│   ├── Helpers/                       # current_company_id(), current_branch_id(), can_user()
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                  # 55 admin controllers (one per module)
│   │   │   ├── Auth/LoginController.php
│   │   │   ├── BranchSwitcherController.php
│   │   │   └── LocaleController.php
│   │   └── Middleware/CheckPermission.php
│   ├── Models/                          # 75 Eloquent models — one per business table
│   ├── Services/StockService.php        # Atomic stock ledger writes
│   └── Providers/                       # AppServiceProvider, AuthServiceProvider
├── config/                              # app.php (locales), permission.php (modules)
├── database/
│   ├── migrations/
│   │   └── 2026_05_13_000000_create_coffee_shop_pos_all_tables.php   # 78 tables (75 business + 3 Laravel built-ins)
│   └── seeders/
│       ├── DatabaseSeeder.php           # Demo company + roles + users
│       ├── PermissionSeeder.php         # Master permissions for all modules
│       └── SampleDataSeeder.php         # Demo branches, menu items, customers
├── lang/
│   ├── en/coffee.php                    # English strings (~300 keys)
│   └── kh/coffee.php                    # Khmer strings (~300 keys)
├── resources/
│   ├── js/
│   │   ├── app.js                       # Inertia + Ziggy + Vue 3 entry
│   │   ├── bootstrap.js                 # axios + global helpers
│   │   ├── Layouts/AdminLayout.vue      # Sidebar + header + collapse toggle
│   │   ├── Pages/Admin/<Module>/Index.vue + Form.vue
│   │   ├── Pages/Auth/Login.vue
│   │   ├── components/                  # ConfirmDelete, FlashToast, FlatpickrInput, TomSelectInput, YajraTable, BsPagination
│   │   └── lang/{en,kh}.json            # Vue-side i18n bundle (mirror of lang/{en,kh}/coffee.php)
│   ├── sass/admin-layout.scss           # Bootstrap-based admin layout (sidebar, header, transitions)
│   └── views/
│       ├── app.blade.php                # Inertia root + jQuery/DataTables CDN scripts
│       └── auth/login.blade.php         # Standalone login page (no Inertia)
├── routes/
│   └── web.php                          # 80+ routes under /admin prefix
├── public/build/                        # Vite output (gitignored)
└── .agents/skills/testing-coffee-shop-pos/SKILL.md   # Testing playbook
```

---

> **ជំពូកទី ២ — ការដំឡើង (Installation & Setup)**

---

## ២.១ តម្រូវការប្រព័ន្ធ (System Requirements)

- **PHP**: 8.2+
- **Composer**: 2.x
- **Node.js**: 18+ (npm or pnpm)
- **Database**: MySQL 8 / MariaDB 10.5+ (recommended for production), or SQLite (default for local)

## ២.២ ជំហានដំឡើង (Step-by-Step)

### ជំហាន ១ — Clone និង Install Dependencies

```bash
git clone https://github.com/sounsonimaura-gif/coffee-shop-pos.git
cd coffee-shop-pos

composer install
npm install
```

### ជំហាន ២ — កំណត់ Environment

```bash
cp .env.example .env
php artisan key:generate
```

បើក `.env` ហើយកែតម្រូវ:

```env
APP_NAME="Coffee Shop POS"
APP_URL=http://127.0.0.1:8000

# Default — SQLite (good for local dev)
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# Production — MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=coffee_pos
# DB_USERNAME=root
# DB_PASSWORD=

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### ជំហាន ៣ — បង្កើត Database, Migrate, និង Seed

```bash
# SQLite only
touch database/database.sqlite

# Migrate + seed demo data
php artisan migrate:fresh --seed
```

> **ចំណាំ**: `migrate:fresh --seed` នឹង:
> - បង្កើតតារាងទាំង **75** (បូកនឹង Laravel built-ins ៣ បន្ថែម = ៧៨)
> - បង្កើតក្រុមហ៊ុនគំរូ **Devin Coffee Co.** (1 company, 1 main branch)
> - បង្កើត 3 roles: **Super Admin**, **Branch Manager**, **Cashier**
> - បង្កើតគណនី login គំរូ (មើល §២.៤ ខាងក្រោម)
> - Seed menu items គំរូ (categories, sizes, items, ingredients, units, suppliers)

### ជំហាន ៤ — Compile Frontend Assets

```bash
# Dev with hot-reload
npm run dev

# Production build (one-shot)
npm run build
```

### ជំហាន ៥ — ចាប់ផ្ដើម Server

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

បើក Browser ទៅ `http://127.0.0.1:8000/login`

## ២.៣ ការត្រួតពិនិត្យដំឡើង (Setup Verification)

| ការត្រួតពិនិត្យ | Command | Expected |
|---|---|---|
| Lint | `vendor/bin/pint --test` | passes |
| Frontend build | `npm run build` | no errors |
| Migrations | `php artisan migrate:status` | 1 migration, status `Ran` |
| Routes | `php artisan route:list \| grep admin \| wc -l` | 80+ routes |
| Login | curl `/login` | HTTP 200 |

## ២.៤ គណនី Default (Demo Logins)

បន្ទាប់ពី `php artisan db:seed` គណនីខាងក្រោមមាន (password ទាំងអស់ = `password`):

| ប្រភេទ | Email | Role |
|---|---|---|
| Super Admin | `admin@coffee.test` | Super Admin — bypass permission checks |
| Manager | `manager@coffee.test` | Branch Manager |
| Cashier | `cashier@coffee.test` | Cashier |

> **សុវត្ថិភាព**: ប្ដូរ password ភ្លាមៗបន្ទាប់ពី deploy ទៅ production!

---

> **ជំពូកទី ៣ — រចនាសម្ព័ន្ធទិន្នន័យ (Database Architecture)**

---

## ៣.១ តារាងទាំងអស់ (All 75 Business Tables)

តារាងត្រូវបានបង្កើតក្នុង file `database/migrations/2026_05_13_000000_create_coffee_shop_pos_all_tables.php`។

| # | ក្រុម (Group) | តារាង | គោលបំណង |
|---|---|---|---|
| 1 | **Tenant & Branches** | `companies`, `branches`, `warehouses`, `branch_user` | Multi-tenant root + sub-branches |
| 2 | **RBAC** | `roles`, `permissions`, `permission_role`, `users` | Manual role/permission system |
| 3 | **POS Locations** | `pos_counters`, `kitchen_stations`, `table_floors`, `table_zones`, `dining_tables` | Physical POS / kitchen / table layout |
| 4 | **Menu Catalog** | `menu_categories`, `menu_items`, `menu_sizes`, `menu_options`, `menu_addons`, `menu_item_size_prices`, `menu_item_options`, `menu_item_addons`, `menu_item_branches` | Menu structure + per-branch availability |
| 5 | **Inventory Master** | `suppliers`, `ingredient_categories`, `units`, `ingredients`, `recipes`, `recipe_items` | Ingredient master + costing |
| 6 | **Inventory Ledger** | `purchases`, `purchase_items`, `stock_batches`, `stock_balances`, `stock_movements`, `stock_adjustments`, `stock_transfers`, `stock_transfer_items`, `waste_records`, `stock_alerts`, `supplier_payments` | Stock movements + balances (centralized through `StockService`) |
| 7 | **Customers & Loyalty** | `customers`, `membership_levels`, `loyalty_point_transactions` | Customer profiles + tiered loyalty |
| 8 | **Promotions** | `promotions`, `promotion_branches`, `promotion_menu_items`, `promotion_menu_categories`, `coupons`, `coupon_redemptions` | Discounts + coupon codes |
| 9 | **Sales / POS** | `cashier_shifts`, `payment_methods`, `orders`, `order_items`, `order_item_modifiers`, `kitchen_orders`, `kitchen_order_items` | Open shift → take orders → push to kitchen |
| 10 | **Invoicing & Payments** | `sale_invoices`, `payments`, `invoice_voids` | Invoice + payment + void log |
| 11 | **Online & Delivery** | `online_orders`, `delivery_orders` | Online channel + delivery tracking |
| 12 | **Expenses** | `expense_categories`, `expenses` | Operating expenses |
| 13 | **HR** | `staff`, `staff_schedules`, `payrolls`, `commissions` | Staff master + scheduling + payroll + commission |
| 14 | **Tax & Settings** | `tax_rates`, `system_settings`, `code_sequences` | Tax + key/value settings + auto-numbering |
| 15 | **Notifications** | `notification_templates`, `notifications` | Template + dispatched notifications |
| 16 | **System / Audit** | `audit_logs`, `login_histories`, `report_exports`, `database_backups` | Read-only system viewers |

## ៣.២ ខ្នាតស្តង់ដារ (Schema Conventions)

ការប្រកាន់ខ្ជាប់ស្តង់ដារនេះត្រូវបានរក្សាក្នុង migration:

- `id` — auto-increment primary key
- `company_id` — multi-tenant scope (NOT NULL on every business table)
- `branch_id` — branch scope (nullable on company-level data eg. roles/permissions)
- `is_active` — boolean visibility flag
- `created_at` / `updated_at` — Laravel timestamps
- `deleted_at` — SoftDeletes where appropriate
- Money columns — `decimal(12,4)` (or `(14,4)` for `stock_value`)
- Quantities — `decimal(14,4)`
- Enum status columns — explicit string enums (e.g. `status: draft|ordered|received|cancelled`)

## ៣.៣ Relationship Diagram (រូបភាពទំនាក់ទំនង — សង្ខេប)

```
Company (1)
├── Branches (N)
│   ├── Warehouses (N)
│   ├── PosCounters (N)
│   ├── KitchenStations (N)
│   ├── TableFloors / TableZones / DiningTables (N)
│   ├── CashierShifts (N)
│   │   └── Orders (N) ──► SaleInvoices (1) ──► Payments (N)
│   └── Staff (N) ──► StaffSchedules / Payrolls / Commissions (N)
├── MenuCategories (N) ──► MenuItems (N) ──► MenuItemSizePrices/Options/Addons (M)
├── Ingredients (N) ──► Recipes (N) ──► RecipeItems (N)
├── Purchases (N) ──► PurchaseItems (N) ──► [StockService::record]
│                                              ├── StockMovements
│                                              ├── StockBalances (weighted avg)
│                                              └── StockBatches
├── Customers (N) ──► LoyaltyPointTransactions (N)
├── Promotions (N) ── M:N ── MenuItems / MenuCategories / Branches
└── Roles (N) ── M:N ── Permissions / Users
```

---

> **ជំពូកទី ៤ — ម៉ូឌុល និងមុខងារទាំងអស់ (All Modules & Features)**

---

មាន **55** controllers ក្រោម `/admin` prefix។ គ្រប់ module មាន Index page (Yajra DataTable) + Form page (create/edit) + show page (បើចាំបាច់) + manual permission check។

## ៤.១ Dashboard

| URL | `/admin` (alias `/admin/dashboard`) |
|---|---|
| ប្រភេទ | Read-only summary |
| ឯកសារ | `app/Http/Controllers/Admin/DashboardController.php` |
| Vue page | `resources/js/Pages/Admin/Dashboard.vue` |

បង្ហាញ KPIs សង្ខេបដែលត្រូវ scope តាម company + active branch:

- ការលក់ថ្ងៃនេះ (Sales Today)
- ការបញ្ជាទិញថ្ងៃនេះ (Orders Today)
- Cashier Shifts ដែលកំពុងបើក (Open Shifts)
- បញ្ជី Low Stock Alerts

## ៤.២ POS — ការលក់ (Sales Flow)

| URL | `/admin/pos` |
|---|---|
| Controller | `PosController` |
| Vue page | `Pages/Admin/Pos/Index.vue` |

ដំណើរការ:

1. **Open Cashier Shift** — `/admin/cashier-shifts` (action: open, opens with starting cash float)
2. **Take Order** — នៅទំព័រ POS, ជ្រើស menu items + sizes + options + addons + qty
3. **Submit Order** — POST ទៅ `/admin/orders` (writes `orders` + `order_items` + auto `kitchen_orders` to relevant `kitchen_stations`)
4. **Generate Invoice** — `/admin/sale-invoices` (POST writes `sale_invoices` from `orders`, calculates `subtotal`, `tax`, `grand_total`)
5. **Take Payment** — POST `/admin/payments` (writes `payments` row(s), supports split tender; updates `sale_invoice.balance_due`)
6. **Close Cashier Shift** — action: close (records ending cash, reconciles `expected_cash` = open float + cash sales − refunds)

### Permission keys សំខាន់ៗ

| Permission | Action |
|---|---|
| `pos.use` | Access POS page |
| `cashier_shifts.open` / `.close` | Open/close shift |
| `orders.create` / `.void` | Place / void orders |
| `sale_invoices.create` / `.void` | Invoice + void invoice |

## ៤.៣ Menu Module

| URL | Controllers |
|---|---|
| `/admin/menu-categories` | `MenuCategoriesController` |
| `/admin/menu-items` | `MenuItemsController` (+ pivot: sizes prices, options, addons, branch availability) |
| `/admin/menu-sizes` | `MenuSizesController` |
| `/admin/menu-options` | `MenuOptionsController` |
| `/admin/menu-addons` | `MenuAddonsController` |

ការបង្កើត Menu Item មួយ ត្រូវកំណត់:
- Category
- Sizes ច្រើនជាមួយ per-size price (writes `menu_item_size_prices`)
- Options (ឧ. iced / hot)
- Addons (ឧ. extra shot, syrup)
- Branch availability (`menu_item_branches`) — ដើម្បីបង្ហាញតែនៅសាខាដែលអនុញ្ញាត

## ៤.៤ Stock Module — ផ្នែកសំខាន់បំផុត

ផ្នែកនេះ ប្រើ **`StockService`** (មើល §៥) ដែលជា centralized ledger។ មុខងាររបស់ Stock module:

| Module | URL | រូបមន្ត |
|---|---|---|
| Ingredients | `/admin/ingredients` | Ingredient master + ingredient categories |
| Units | `/admin/units` | មាត្រា (g, ml, pc, …) — base + conversion factor |
| Suppliers | `/admin/suppliers` | Supplier master + contact info |
| **Purchases** | `/admin/purchases` | កុំទិញវត្ថុធាតុដើម។ ត្រូវ select supplier + warehouse + items, save with `status=received` ⇒ writes `stock_movements`, `stock_balances`, `stock_batches`, calculates `grand_total = subtotal + tax + shipping − discount` |
| **Stock Transfers** | `/admin/stock-transfers` | ផ្ទេរ stock ពី warehouse មួយទៅមួយ (writes 2× `stock_movements` — out at source, in at destination) |
| **Stock Adjustments** | `/admin/stock-adjustments` | កែប្រែ qty (loss, found, recount) — writes `stock_movements` with `movement_type=adjustment` |
| **Waste Records** | `/admin/waste-records` | ការខាតបង់ (expired / damaged) — writes negative `stock_movements` |
| **Recipes** | `/admin/recipes` | Auto-calculated `estimated_cost = SUM(recipe_items.qty × ingredient.cost_per_unit)` |
| **Stock Alerts** | `/admin/stock-alerts` (read-only) | បង្ហាញ ingredients ដែលនៅ ≤ reorder level + acknowledge action |

### អ្នកមិនអាចកែ `stock_movements` ដោយផ្ទាល់!

`stock_movements`, `stock_balances`, `stock_batches` ត្រូវសរសេរបានតែតាមរយៈ `StockService` ប៉ុណ្ណោះ — មិនមាន CRUD UI ដោយផ្ទាល់ទេ។ មាន**ការមើល (read-only viewer)** ប៉ុណ្ណោះ ដោយសារ business invariants ដូចជា `balance_after = sum(in − out)` ត្រូវរក្សា។

## ៤.៥ POS Locations (Counters / Stations / Tables)

| Module | URL |
|---|---|
| POS Counters | `/admin/pos-counters` |
| Kitchen Stations | `/admin/kitchen-stations` |
| Table Floors | `/admin/table-floors` |
| Table Zones | `/admin/table-zones` |
| Dining Tables | `/admin/dining-tables` |

កំណត់ការទាក់ទាញរូបវិទ្យានៃកន្លែងលក់ + ផ្ទះបាយ + តុ — នៅ POS time, cashier ជ្រើស counter + dining table ហើយ orders ត្រូវ route ទៅ kitchen station ត្រឹមត្រូវ។

## ៤.៦ Customer & Loyalty

| Module | URL |
|---|---|
| Customers | `/admin/customers` |
| Membership Levels | `/admin/membership-levels` |
| Loyalty Points | `/admin/loyalty-point-transactions` (read-only viewer) |

លំនាំការងារ:
- បង្កើត Membership Levels (Bronze / Silver / Gold) ជាមួយ `discount_percent` និង `points_per_dollar`
- Customers មាន `current_membership_level_id` + `total_points`
- នៅ POS time អ្នកអាច attach customer ទៅ order ⇒ system auto issue `loyalty_point_transactions` (earn) និងពេលប្រើ points វា auto issue redemption

## ៤.៧ Promotions & Coupons

| Module | URL |
|---|---|
| Promotions | `/admin/promotions` (link ទៅ branches + menu items + categories) |
| Coupons | `/admin/coupons` |

Promotions អាច scope តាម:
- Branch (`promotion_branches`)
- Menu Item (`promotion_menu_items`)
- Menu Category (`promotion_menu_categories`)
- Time range (`starts_at`, `ends_at`)
- Type: `percent` / `fixed_amount` / `buy_x_get_y`

Coupons ត្រូវកំណត់ `code`, `discount_value`, `usage_limit`, `expires_at` — ការប្រើនីមួយៗត្រូវកត់ត្រាក្នុង `coupon_redemptions`។

## ៤.៨ Online & Delivery Orders

| Module | URL |
|---|---|
| Online Orders | `/admin/online-orders` |
| Delivery Orders | `/admin/delivery-orders` |

`online_orders` representsការបញ្ជាតាមឆានែល external (Facebook, Telegram, website) — នៅពេល confirm វា auto-link ទៅ `orders` table។ `delivery_orders` track delivery status (`pending`, `assigned`, `picked_up`, `delivered`, `failed`)។

## ៤.៩ HR Module

| Module | URL | Auto-calc |
|---|---|---|
| Staff | `/admin/staff` | — |
| Staff Schedules | `/admin/staff-schedules` | — |
| **Payrolls** | `/admin/payrolls` | `net_salary = basic + commission + bonus − deduction` (calculated both in Vue form + backend on save) |
| **Commissions** | `/admin/commissions` | `commission_amount = base_amount × rate / 100` (for percent type) ឬ ប្រើ fixed amount |

## ៤.១០ Expenses

| Module | URL |
|---|---|
| Expense Categories | `/admin/expense-categories` |
| Expenses | `/admin/expenses` |

កត់ត្រាការចំណាយ (rent, utilities, supplies) ដោយ category, attach receipt, link ទៅ branch។

## ៤.១១ Settings & Reports

| Module | URL |
|---|---|
| Payment Methods | `/admin/payment-methods` |
| Tax Rates | `/admin/tax-rates` (percent ឬ fixed; can apply per item, per order, or both) |
| System Settings | `/admin/settings` (key/value JSON store) |
| Notification Templates | `/admin/notification-templates` |
| Code Sequences | `/admin/code-sequences` (auto-numbering: invoices, orders, purchases, transfers, …) |
| Reports — Sales | `/admin/reports/sales` |
| Reports — Inventory | `/admin/reports/inventory` |
| Reports — Expenses | `/admin/reports/expenses` |

## ៤.១២ System Viewers (Read-only)

ផ្ទាំងមើលប្រវត្តិសារ — នៅក្នុង sidebar group **"System"**:

| Module | URL | កម្រិត |
|---|---|---|
| Audit Logs | `/admin/audit-logs` | View |
| Login Histories | `/admin/login-histories` | View |
| Stock Alerts | `/admin/stock-alerts` | View + Acknowledge |
| Loyalty Points | `/admin/loyalty-point-transactions` | View |
| Notifications | `/admin/notifications` | View |
| Database Backups | `/admin/database-backups` | View |
| Report Exports | `/admin/report-exports` | View |

---

> **ជំពូកទី ៥ — StockService (ផ្នែកស្នូលនៃ Inventory)**

---

ឯកសារ: `app/Services/StockService.php`

`StockService` គឺជា **ledger តែមួយ** សម្រាប់ការផ្លាស់ប្ដូរ stock ទាំងអស់។ មិនមាន controller ណាមួយ touch `stock_movements`, `stock_balances`, `stock_batches` ដោយផ្ទាល់ — ទាំងអស់ត្រូវឆ្លងកាត់សេវានេះ។

## ៥.១ Method សំខាន់

```php
StockService::record(
    company_id: int,
    branch_id: int,
    warehouse_id: int,
    ingredient_id: int,
    quantity_in: float,        // positive for inbound (purchase, transfer-in, found)
    quantity_out: float,       // positive for outbound (sale, transfer-out, waste)
    unit_cost: float,
    movement_type: string,     // purchase_in | transfer_in | transfer_out | adjustment | waste | sale | recipe_consume
    reference_type: string,    // App\Models\Purchase, StockTransfer, …
    reference_id: int,
    batch_no?: string,
    expiry_date?: Carbon
): StockMovement
```

ការបង្កើតរបស់ method នេះ atomic (transaction):

1. បង្កើត row ថ្មីក្នុង `stock_movements` ជាមួយ `balance_after = (current_balance + qty_in − qty_out)`
2. Upsert row ក្នុង `stock_balances` (per ingredient + warehouse) ជាមួយ:
   - `quantity_on_hand` — running total
   - `weighted_avg_cost` — recalculated weighted average (purchase) ឬ unchanged (consume/transfer)
   - `stock_value = quantity_on_hand × weighted_avg_cost`
3. បើ batch_no ត្រូវផ្ដល់ — upsert row ក្នុង `stock_batches` (FIFO consumption)

## ៥.២ ការត្រួតពិនិត្យ (Verifying)

បន្ទាប់ពី save Purchase ដែលមាន `status=received`, run:

```bash
php artisan tinker --execute='
  echo App\Models\StockMovement::latest("id")->first()?->toJson() . PHP_EOL;
  echo App\Models\StockBalance::latest("id")->first()?->toJson() . PHP_EOL;
'
```

ការត្រួតពិនិត្យ:
- `movement_type = "purchase_in"`
- `quantity_in` = qty ក្នុង form
- `unit_cost` = unit_cost ក្នុង form
- `balance_after` = `stock_balance.quantity_on_hand`
- `stock_value` = `quantity_on_hand × unit_cost` (or weighted_avg)

ប្រសិនបើ values មិនត្រឹមត្រូវ ⇒ `StockService::record` regressed។ មើល <ref_file file="/home/ubuntu/repos/coffee-shop-pos/app/Services/StockService.php" /> ដើម្បីបញ្ជាក់។

---

> **ជំពូកទី ៦ — Multi-tenancy & Branch Switching**

---

## ៦.១ Tenant Scoping

គ្រប់ admin controller scoped queries តាម `company_id` ដែលដក់ចេញពី authenticated user។ មិនមាន global tenancy package — query scoping ត្រូវបានធ្វើដោយផ្ទាល់៖

```php
Order::query()
    ->where('company_id', current_company_id())
    ->where('branch_id', current_branch_id())   // optional
```

`current_company_id()` និង `current_branch_id()` ជា helper functions ក្នុង `app/Helpers/helpers.php`។

## ៦.២ ការប្ដូរ Branch (Branch Switching)

នៅ top-bar dropdown **"Main Branch"** អ្នកអាចជ្រើស branch មួយផ្សេង (ប្រសិនបើគណនីអ្នកត្រូវបាន assign ទៅ branch ច្រើនតាមរយៈ `branch_user` pivot)។

- POST ទៅ `/branch/switch` (BranchSwitcherController)
- Save ទៅ session key `active_branch_id`
- Share ឡើង Vue តាមរយៈ Inertia shared prop `auth.branch`
- Page reload-less — Inertia partial reload គ្រាន់តែ rehydrate `auth.branch` prop

---

> **ជំពូកទី ៧ — សិទ្ធិ និងតួនាទី (Roles & Permissions)**

---

ប្រព័ន្ធនេះ **មិនប្រើ Spatie/laravel-permission** — RBAC ត្រូវ implement ដោយផ្ទាល់ដើម្បីការគ្រប់គ្រងផ្ទាល់នៅលើ schema។

## ៧.១ តារាងសំខាន់

| តារាង | គោលបំណង |
|---|---|
| `roles` | Role definitions, scoped to company (or NULL = system role) |
| `permissions` | Master list of permission keys (e.g. `orders.create`, `pos.use`) |
| `permission_role` | M:N pivot — which roles have which permissions |
| `users` | Each user has `role_id` reference |

## ៧.២ មុខងារ Helper

```php
// In any controller/Blade/Vue (via Inertia auth.user.permissions)
can_user('orders.create');
$user->hasPermission('pos.use');
```

ការត្រួតពិនិត្យ Permission ក្នុង routes ត្រូវ wrap ដោយ **`CheckPermission`** middleware:

```php
Route::middleware(['auth', 'permission:orders.create'])
    ->post('/admin/orders', [OrdersController::class, 'store']);
```

## ៧.៣ Default Roles

| Role | Description | Permissions |
|---|---|---|
| **Super Admin** | System-wide, all permissions | All |
| **Branch Manager** | Operations at branch level | Menu/Stock/Customers/POS/Reports + most CRUD |
| **Cashier** | POS-only role | `pos.use`, `cashier_shifts.*`, `orders.*`, `payments.*` |

Permission keys ត្រូវបានកំណត់ក្នុង `database/seeders/PermissionSeeder.php`។ មាន **~270 permissions** ឆ្លងកាត់ ~60 modules។

## ៧.៤ ការបន្ថែម Permission ថ្មី

1. បន្ថែម entry ទៅ `PermissionSeeder::$modules`
2. Run `php artisan db:seed --class=PermissionSeeder`
3. Assign ទៅ roles តាមរយៈ Roles admin page

---

> **ជំពូកទី ៨ — KH/EN Language Switching**

---

## ៨.១ How It Works

- **Server-side strings** ស្ថិតក្នុង `lang/en/coffee.php` និង `lang/kh/coffee.php` (~300 keys នីមួយៗ)
- **Vue-side strings** ស្ថិតក្នុង `resources/js/lang/en.json` និង `resources/js/lang/kh.json` (mirror)
- Active locale stored in session key `locale` + shared to Vue via Inertia prop `locale`
- Vue `t(key)` helper resolves from `lang/<locale>.json`
- Server `__()` Blade helper resolves from `lang/<locale>/coffee.php`

## ៨.២ Switcher Flow

1. Click top-right **EN ▾** dropdown → select Khmer
2. POST ទៅ `/locale` with `locale=kh`
3. Backend saves `session('locale')` + returns Inertia partial response
4. Vue updates `t()` reactive bundle without full page reload
5. Sidebar labels, page titles, page body all reactively re-translate

## ៨.៣ Yajra Column Headers — Special Handling

នៅ Yajra DataTables, column titles ត្រូវ resolved ម្ដងតែម៉ោង server time (`$columns = [...]`)។ ដើម្បីឲ្យ headers re-translate ដោយមិន reload:

- Server sends **raw `coffee.xxx` keys** (មិន translate)
- Vue `YajraTable` component calls `t(column.title)` reactively ⇒ re-translate ចំពោះ DOM ដោយផ្ទាល់

## ៨.៤ ការបន្ថែម Translation Key ថ្មី

1. បន្ថែម key ក្នុង `lang/en/coffee.php` + `lang/kh/coffee.php`
2. Mirror ទៅ `resources/js/lang/en.json` + `resources/js/lang/kh.json`
3. ប្រើជា `__('coffee.your_key')` (Blade) ឬ `t('coffee.your_key')` (Vue)

## ៨.៥ Khmer Font Rendering

ប្រសិនបើ Khmer glyphs បង្ហាញត្រឹមត្រូវ ⇒ ត្រូវដំឡើង Noto Sans Khmer font លើ server / browser:

```bash
sudo apt-get install -y fonts-noto fonts-noto-color-emoji
```

---

> **ជំពូកទី ៩ — Layout, Sidebar, Header**

---

## ៩.១ Custom Admin Layout

ឯកសារ: `resources/sass/admin-layout.scss`

ប្រព័ន្ធ **មិនប្រើ Skodash theme** ទេ (ដែលជា theme ដែលផ្ដល់មកដំបូងតែខ្វះ assets)។ Layout ស្ថិតលើ Bootstrap 5 + custom SCSS។

### Breakpoint Logic

| Viewport | សិន្ទន | Toggle behaviour |
|---|---|---|
| ≥ 992px (desktop) | `.wrapper.sidebar-collapsed` | Sidebar slides offscreen via `transform: translateX(-100%)`; content + header span full width |
| < 992px (mobile) | `.wrapper.sidebar-open` | Sidebar opens as overlay with dim backdrop; backdrop click closes |

Vue logic: `resources/js/Layouts/AdminLayout.vue`

## ៩.២ Header Dropdowns

នៅ top-bar មាន dropdowns ៣ (Branch / EN-KH / User), ស្ថិតក្នុង `Layouts/AdminLayout.vue`។ ស្តាយ៖

- Small custom chevron (no Bootstrap default ▼)
- Hover state: light grey
- Active item: solid blue + bold
- Menu container: rounded + drop shadow
- User card: avatar + name + role

## ៩.៣ Sidebar Accordion

- Pure Vue-driven (no metismenu / external library)
- Parent click: toggle expanded state
- Active route auto-expand parent group
- Sidebar order match migration table order (POS top → System bottom)

---

> **ជំពូកទី ១០ — Gotchas សំខាន់ៗ**

---

## ១០.១ jQuery + DataTables MUST be classic CDN scripts

ឯកសារ `resources/views/app.blade.php` ត្រូវ load jQuery + DataTables **before** `@vite` as classic `<script>` tags:

```html
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
@vite(['resources/js/app.js'])
```

**WHY**: DataTables' UMD wrapper មាន `window = ...` / `document = ...` assignments នៅ top-level ដែល crash នៅក្នុង strict-mode ESM bundles (Vite)។ ប្រសិនបើនរណាម្នាក់ import វាតាមរយៈ Vite ⇒ ផ្ទាំង login នឹង render ទទេ។

## ១០.២ Vite Build វាមាន Caching

ប្រសិនបើ CSS/SCSS ប្រែប្រួល មិនបង្ហាញ ⇒ run:

```bash
rm -rf public/build
npm run build
```

ឬ ប្រើ `npm run dev` ដែលមាន hot reload។

## ១០.៣ Tom Select Dropdowns

Tom Select wrapping a `<select>` តម្រូវអោយ click លើ wrapper មុនបើ dropdown — ការ type ចូលដោយផ្ទាល់នឹង filter ប៉ុណ្ណោះ មិន commit value ទេ។

## ១០.៤ Khmer Font

ប្រសិនបើ glyphs Khmer បង្ហាញខូច (subscripts stacking ខុស) ⇒ install Noto Sans Khmer:

```bash
sudo apt-get install -y fonts-noto fonts-noto-color-emoji
```

## ១០.៥ Demo Seeder ខ្វះ Fixtures

ការ Seed default មិន create:
- Sample ingredients with realistic costs
- Sample suppliers
- Sample staff

ដើម្បីសាកល្បង modules ដែលត្រូវការ relations នេះ:

```bash
php artisan tinker --execute='
  App\Models\Ingredient::firstOrCreate(["company_id"=>1,"name"=>"Arabica Beans"], ["unit_id"=>1,"is_active"=>1]);
  App\Models\Supplier::firstOrCreate(["company_id"=>1,"name"=>"Acme Coffee Supplies"], ["is_active"=>1]);
  App\Models\Staff::firstOrCreate(["company_id"=>1,"first_name"=>"Test","last_name"=>"Barista"], ["branch_id"=>1,"is_active"=>1]);
  App\Models\Warehouse::firstOrCreate(["company_id"=>1,"warehouse_code"=>"WH-MAIN"], ["branch_id"=>1,"name"=>"Main Warehouse","is_active"=>1]);
'
```

---

> **ជំពូកទី ១១ — Workflow ឆ្នាំ Operations**

---

## ១១.១ ការបើកហាងសម្រាប់ថ្ងៃនេះ (Open Day)

1. Login ជា Cashier ⇒ `/admin/cashier-shifts/open` ⇒ ដាក់ starting cash float
2. Verify menu items active + stock ready (មើល `/admin/stock-alerts` បើ alerts ច្រើន ត្រូវ purchase មុន)

## ១១.២ ការប្រតិបត្តិការ POS

1. Customer arrives ⇒ open `/admin/pos`
2. ជ្រើស dining table (បើទាន់) + counter
3. Click menu items, choose sizes + addons
4. Submit ⇒ creates order + kitchen orders
5. ការសម្រេច order ⇒ click "Generate Invoice" ⇒ POST payment

## ១១.៣ End-of-Day

1. Print Z-Report (sales summary)
2. Close cashier shift ⇒ ការប្រកាស ending cash
3. ប្រព័ន្ធ compute variance = `expected − ending` + flag
4. Reconcile stock alerts ⇒ schedule purchases for next day

## ១១.៤ End-of-Month

1. Run payroll calculation (mass) ⇒ `/admin/payrolls`
2. Generate commission report
3. Generate report exports (sales summary, inventory valuation, expense breakdown)
4. Database backup ⇒ មើល `/admin/database-backups`

---

> **ជំពូកទី ១២ — សំណួរញឹកញាប់ (FAQ)**

---

| សំណួរ | ចម្លើយ |
|---|---|
| តើតារាង Stock Movements អាចកែប្រែបានទេ? | មិនបាន - ត្រូវឆ្លងកាត់ `StockService` ប៉ុណ្ណោះ |
| តើ Cashier អាចបើក Shift ច្រើនជា ១ ក្នុងម៉ោងតែ មួយទេ? | មិនបាន — system ការពារដោយ business rule (one open shift per user) |
| តើខ្ញុំអាច disable user ដោយមិន delete បានទេ? | បាន — set `is_active = false` |
| តើ multi-branch user ត្រូវ assign យ៉ាងណា? | តាមរយៈ `branch_user` pivot នៅ `/admin/users` ⇒ checkbox |
| តើ KH/EN switch ត្រូវ reload page ទេ? | មិនត្រូវ — Inertia partial reload តែ rehydrate locale prop |
| តើ DataTable column headers រស់រវើកនៅពេលប្ដូរ language ទេ? | រស់ — server sends raw keys, Vue `t()` reactively translate |
| ហេតុអ្វី login page render blank? | DataTables ត្រូវបាន import តាមរយៈ Vite — ត្រូវប្តូរទៅ CDN script (មើល §១០.១) |
| តើ session timeout ប៉ុន្មាន? | Default Laravel — 120 minutes (`SESSION_LIFETIME` ក្នុង .env) |
| តើខ្ញុំអាច export reports ទៅ PDF/Excel ទេ? | បាន — `/admin/report-exports` queue export jobs |
| ហេតុអ្វី Sidebar មិនប្រែទំហំនៅពេល click hamburger? | មុនជួសជុលក្នុង PR #7 — បច្ចុប្បន្នវាគួរ slide offscreen ហើយ |

---

> **ជំពូកទី ១៣ — Commands សំខាន់ៗ**

---

```bash
# Setup / Reset
php artisan migrate:fresh --seed
composer install
npm install && npm run build

# Dev
php artisan serve --host=127.0.0.1 --port=8000
npm run dev

# Code Quality
vendor/bin/pint                    # PHP formatter (apply)
vendor/bin/pint --test             # PHP formatter (verify)

# Permissions / Seeders
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=SampleDataSeeder

# Database utilities
php artisan tinker
php artisan migrate:status
php artisan route:list | grep admin

# Backup (writes to database_backups table)
php artisan backup:run
```

---

> **ជំពូកទី ១៤ — Reference**

---

## ១៤.១ Routes summary (~83 routes)

ត្រួតពិនិត្យ:

```bash
php artisan route:list | grep "admin\." | wc -l
```

## ១៤.២ Models summary (75)

ត្រួតពិនិត្យ:

```bash
ls app/Models/ | wc -l
```

## ១៤.៣ Translation keys summary

```bash
grep -c "=>" lang/en/coffee.php
grep -c "=>" lang/kh/coffee.php
grep -c ":" resources/js/lang/en.json
grep -c ":" resources/js/lang/kh.json
```

ជាបច្ចុប្បន្ន **~300 keys** ឆ្លងកាត់ 4 ផ្ទាំង — ត្រូវរក្សា 4 ផ្ទាំងសម្បូរបាននិងស្មើ។

## ១៤.៤ Pull Requests merged

| PR | ប្រធានបទ |
|---|---|
| #1 | Pass 2 sub-modules (purchases, transfers, adjustments, waste, HR, recipes, notifications, online/delivery orders) |
| #2 | Fix Vite bundling — load jQuery + DataTables from CDN |
| #4 | Audit fixes — i18n sync, Yajra re-translation, 7 read-only system viewers |
| #5 | Layout fix — custom admin SCSS (replace missing Skodash theme) |
| #6 | Header dropdown polish (branch / locale / user) |
| #7 | Sidebar collapse fix — slide offscreen on desktop |

---

> **ឯកសារយោងបន្ថែម (Additional References)**

- Migration source-of-truth: `database/migrations/2026_05_13_000000_create_coffee_shop_pos_all_tables.php`
- Audit report (PR #4): `AUDIT.md`
- Testing skill: `.agents/skills/testing-coffee-shop-pos/SKILL.md`
- GitHub repo: https://github.com/sounsonimaura-gif/coffee-shop-pos

---

*ឯកសារនេះត្រូវបាន update ដោយ Devin AI — ប្រសិនបើអ្នករកឃើញចំណុចអ្វីខុស សូម file issue នៅ GitHub។*
