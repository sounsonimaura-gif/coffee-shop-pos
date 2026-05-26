---
name: testing-coffee-shop-pos
description: End-to-end test the Coffee Shop POS admin app (Laravel 12 + Vue 3 + Inertia + Yajra DataTables). Use when verifying CRUD modules, the StockService pipeline, payroll/recipe auto-calculations, the KH/EN language switcher, or layout/sidebar behavior.
---

# Testing the Coffee Shop POS

## Dev server

```bash
cd ~/repos/coffee-shop-pos
php artisan migrate:fresh --seed   # only if you need a clean DB
php artisan serve --host=127.0.0.1 --port=8000
```

If the UI renders blank, check `resources/views/app.blade.php` still loads jQuery + DataTables as **classic** `<script>` tags from the CDN **before** `@vite`. DataTables' UMD wrapper has bare `window = …` assignments that crash in strict-mode ESM bundles, so they must NOT be imported through Vite. If they ever get re-imported in `resources/js/bootstrap.js`, login will be a blank page.

## Demo logins

All passwords are `password`:
- `admin@coffee.test` — Super Admin (use for testing; bypasses permission checks)
- `manager@coffee.test`
- `cashier@coffee.test`

## Fixtures required before testing the new modules

The demo seeder does NOT create these. Seed them via tinker before recording tests, otherwise the create forms have empty dropdowns or fail validation:

```bash
php artisan tinker --execute='
  App\Models\Ingredient::firstOrCreate(["company_id"=>1,"name"=>"Arabica Beans"], ["unit_id"=>1,"is_active"=>1]);
  App\Models\Supplier::firstOrCreate(["company_id"=>1,"name"=>"Acme Coffee Supplies"], ["is_active"=>1]);
  App\Models\Staff::firstOrCreate(["company_id"=>1,"first_name"=>"Test","last_name"=>"Barista"], ["branch_id"=>1,"is_active"=>1]);
  App\Models\Warehouse::firstOrCreate(["company_id"=>1,"warehouse_code"=>"WH-MAIN"], ["branch_id"=>1,"name"=>"Main Warehouse","is_active"=>1]);
'
```

## The four highest-signal assertions for Pass 2

If you only have time for a smoke test, run these four — each one catches a specific class of regression in the StockService / line-item / auto-calc code paths.

### 1. Purchase save writes a stock_movement + stock_balance (most important)

Create a purchase with status=`received` and 1 line item, then verify:

```bash
php artisan tinker --execute='
  echo App\Models\StockMovement::latest("id")->first()?->toJson() . PHP_EOL;
  echo App\Models\StockBalance::latest("id")->first()?->toJson() . PHP_EOL;
'
```

Expect `movement_type=purchase_in`, `quantity_in` matches form qty, `unit_cost` matches form cost, `balance_after` == `stock_balance.quantity_on_hand`, `stock_value = qty * cost`. Failure here means `StockService::record` regressed.

### 2. Payroll auto net_salary

Form fields: basic, commission, bonus, deduction. The in-form Net Salary display + the saved row should both equal `basic + commission + bonus - deduction`. Sign/order bugs will produce a visibly different number.

### 3. Recipe estimated_cost roll-up

Add 2+ ingredient lines with different qty/cost. The form footer + saved row's `estimated_cost` should equal the sum of `qty * cost_per_unit` across lines. A broken roll-up usually shows 0, the last line only, or just the first line.

### 4. KH/EN switcher

On an index page, click the top-right `EN ▾` toggle and select Khmer. Expect URL unchanged, no full page flash, sidebar parent labels and page body translated.

**Known issues (NOT regressions — file as follow-ups if the user cares):**
- **Yajra DataTable column headers do not re-translate** on hot switch — they read `column.title` once at DataTable init. Visible symptom: `Code`, `Grand Total`, `Status`, and raw keys like `coffee.date`, `coffee.supplier`, `coffee.payment_status` stay in English/raw in Khmer mode. Fix would be to destroy + re-init the DataTable when locale changes, or to translate column titles server-side per request.
- **A few sidebar items render as raw keys** in both EN and KH because they're missing from `lang/en/coffee.php` and `lang/kh/coffee.php`: `hr`, `online_orders_module`, `recipes`, `staff_schedules`, `commissions`.
- **Khmer glyph rendering**: the test VM may not have Khmer fonts installed. If glyphs look broken/stacked, install `fonts-noto` (e.g. `sudo apt-get install -y fonts-noto fonts-noto-color-emoji` or add to the env blueprint). The i18n strings themselves are correct in the DOM — only rendering is affected.

## Sidebar / viewport testing

The admin layout uses **two distinct paths** for the sidebar toggle, keyed off viewport width. When testing layout/header/sidebar changes, exercise BOTH paths:

### Breakpoint and classes

- **Desktop (≥992px)**: `toggleSidebar()` flips `sidebarCollapsed.value` → adds `.sidebar-collapsed` to `.wrapper`. Sidebar slides offscreen via `transform: translateX(-100%)`; `.top-header { left }` zeros; `.wrapper { padding-left }` zeros.
- **Mobile (<992px = `@media (max-width: 991.98px)`)**: sidebar starts hidden. Toggle flips `sidebarOpenMobile.value` → adds `.sidebar-open` to `.wrapper`; sidebar slides in as an overlay with a backdrop. Backdrop click closes it.

The Vue logic lives in `resources/js/Layouts/AdminLayout.vue`; the CSS lives in `resources/sass/admin-layout.scss`.

### Simulating a mobile viewport in the test VM

The GUI runs at 1024x768, which falls into the desktop breakpoint. To test the mobile branch, resize the Chrome window narrower than 992px:

```bash
# Switch Chrome to a ~700px-wide mobile-ish viewport
DISPLAY=:0 wmctrl -r :ACTIVE: -b remove,maximized_vert,maximized_horz
DISPLAY=:0 wmctrl -r :ACTIVE: -e 0,0,0,700,768   # gravity, x, y, width, height

# Restore to maximized desktop
DISPLAY=:0 wmctrl -r :ACTIVE: -b add,maximized_vert,maximized_horz
```

After resizing, wait a beat and re-screenshot — the responsive layout uses CSS media queries (no JS resize-handler needed). Don't use Chrome DevTools device emulation for this; just resize the OS window. Resize back to maximized BEFORE recording the next desktop test so the rest of the recording looks consistent.

### Common layout-regression triggers to watch for

- A new CSS rule on `.sidebar-wrapper` that omits a `transform` baseline — the transition won't animate cleanly bidirectionally.
- A new media query that overrides `.sidebar-wrapper { transform }` on mobile — makes the overlay flash.
- A new wrapper class added in `AdminLayout.vue` that doesn't have a corresponding SCSS rule — makes the toggle look like it does nothing.

## Recording tips

- Test purely via the UI — do not POST forms via `curl` to the admin endpoints, the auth/CSRF flow is session-cookie based and DataTable AJAX endpoints expect the Inertia request shape.
- Always run the DB-verification `tinker` commands **after** each save — they're cheap and catch the exact failure mode ("row never persisted") that a green toast hides.
- **Maximize Chrome before starting the recording**: `sudo apt-get install -y wmctrl 2>/dev/null; DISPLAY=:0 wmctrl -r :ACTIVE: -b add,maximized_vert,maximized_horz`. Doing this AFTER recording starts results in a recording that begins with a half-window.
- Annotation style: one `test_start` per of the 4 tests above, then 1–2 consolidated `assertion`s per test (form-state + post-save state).
- For sidebar/layout tests, capture a clear before/after pair: one screenshot with sidebar visible, one with sidebar collapsed/offscreen. Side-by-side comparison is the most readable evidence for layout PRs.

## Common gotchas

- Tom Select inputs require clicking the wrapper first to open the dropdown, then clicking the option — typing into the input filters but doesn't always commit.
- The address bar at y=40 may not always accept clicks reliably via the computer tool; if a URL change appears to no-op, navigate by clicking a sidebar link instead (sidebar links emit Inertia visits which are equivalent).
- The default screen is 1024x768. If a layout looks broken at this width, check whether it's the test viewport or a real CSS bug — try maximizing first.
