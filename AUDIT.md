# Coffee Shop POS — Project Audit vs. Migration

**Baseline:** `database/migrations/2026_05_13_000000_create_coffee_shop_pos_all_tables.php` (78 tables incl. 3 Laravel built-ins).
**Audited tip:** `dd7353d` (head of `devin/1778691217-coffee-shop-pos-foundation`, which currently contains PRs #1 + #2 + #3 — see "Branch state" below).
**Result:** all 75 business tables have a Model. All CRUD modules promised in the original spec have a Controller, routes, sidebar entries, and Vue Index + Form pages. A handful of read-only system tables had no admin viewer; those are added in this PR.

---

## Branch state (action needed)

`origin/main` is still at the very first scaffold commit (`0fd86e3`). Every merged PR (#1 Pass 2, #2 Vite fix, #3 Skill) was merged into `devin/1778691217-coffee-shop-pos-foundation`, **not** into `main`. The "real" tip of the project is `dd7353d` on that foundation branch.

```
dd7353d (origin/devin/...-foundation)  Merge PR #3 Skill
6a2a5de                                Merge PR #2 Vite fix
eabf307                                Merge PR #1 Pass 2 sub-modules
0fd86e3 (origin/main)                  feat: scaffold
```

This audit PR is opened from a branch off `dd7353d` and targets `main`, so merging it brings everything (Pass 2 + Vite fix + Skill + audit fixes) onto `main` in a single move.

---

## Gap matrix

Legend: ✅ complete · ⚠ partial · ➕ added in this PR · — N/A (system / pivot / child-of-parent).

### Core entities (CRUD)
| Table | Model | Controller | Routes | Sidebar | Form | Permissions | i18n |
|---|---|---|---|---|---|---|---|
| companies | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| branches | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| warehouses | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| users | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| roles | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| permissions | ✅ | ✅ (read-only) | ✅ | ✅ | — | ✅ | ✅ |
| menu_categories | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| menu_items | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| menu_sizes | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| menu_options | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| menu_addons | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| ingredient_categories | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| ingredients | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| units | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| suppliers | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| customers | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| membership_levels | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| promotions | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| coupons | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| payment_methods | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| tax_rates | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| expense_categories | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| expenses | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| pos_counters | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| kitchen_stations | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| table_floors | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| table_zones | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| dining_tables | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| purchases (+ items) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| stock_transfers (+ items) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| stock_adjustments | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| waste_records | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| recipes (+ items) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| staff | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| staff_schedules | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| payrolls | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| commissions | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| online_orders | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| delivery_orders | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| notification_templates | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| orders | ✅ | ✅ (POS-driven) | ✅ | ✅ | — | ✅ | ✅ |
| sale_invoices | ✅ | ✅ (read-only) | ✅ | ✅ | — | ✅ | ✅ |
| cashier_shifts | ✅ | ✅ (open/close) | ✅ | ✅ | — | ✅ | ✅ |
| system_settings | ✅ | ✅ (singleton) | ✅ | ✅ | ✅ | ✅ | ✅ |
| audit_logs | ✅ | ✅ (read-only) | ✅ | ✅ | — | ✅ | ✅ |

### System / read-only tables (no business CRUD needed)
| Table | Model | Admin viewer | Notes |
|---|---|---|---|
| notifications | ✅ | ➕ added | Inbox view per user. |
| login_histories | ✅ | ➕ added | Read-only Yajra list. |
| stock_alerts | ✅ | ➕ added | Read-only list w/ acknowledge button. |
| loyalty_point_transactions | ✅ | ➕ added | Read-only list, scoped by customer. |
| code_sequences | ✅ | ➕ added | Read + edit-prefix. |
| database_backups | ✅ | ➕ added | List + download + delete. |
| report_exports | ✅ | ➕ added | List + download. |

### Pivots / child-of-parent (managed inside parent's form, no standalone CRUD)
`branch_user`, `permission_role`, `menu_item_size_prices`, `menu_item_options`, `menu_item_addons`, `menu_item_branches`, `promotion_branches`, `promotion_menu_items`, `promotion_menu_categories`, `purchase_items`, `recipe_items`, `stock_transfer_items`, `order_items`, `order_item_modifiers`, `kitchen_orders`, `kitchen_order_items`, `payments`, `invoice_voids`, `supplier_payments`, `coupon_redemptions`, `stock_batches`, `stock_balances`, `stock_movements` — all Models exist; mutation is owned by parent form / StockService / POS flow.

### Laravel built-ins (no app code needed)
`password_reset_tokens`, `sessions` — handled by Laravel framework.

---

## Other gaps fixed in this PR

1. **JS translation bundle out of sync with PHP files.**
   `lang/{en,kh}/coffee.php` was extended in Pass 2 (added `hr`, `recipes`, `commissions`, `staff_schedules`, `online_orders_module`, etc.) but `resources/js/lang/{en,kh}.json` was never regenerated — so the Vue sidebar showed those keys raw. Fixed by regenerating both JSON files from the PHP files and adding a small `php artisan i18n:sync-js` command so they can't drift again.

2. **Missing PHP keys** used by `PurchasesController`: `coffee.date`, `coffee.supplier`, `coffee.payment_status`. Added with EN + KH translations.

3. **Yajra DataTable column headers stuck in original locale after switching.**
   Root cause: the controller called `__($c['title'])` server-side during `index()`, so the page received already-translated strings (`"Branch Code"`, not the key). Partial Inertia reloads after `/locale` POST do not refresh `columns`, so headers never updated.
   Fix: the controller now sends the **raw translation key** (e.g. `'coffee.branch_code'`); the Vue `Index.vue` translates it through `useI18n.t()` inside `renderColumns()` so the reactive `locale` triggers re-build of the DataTable with the new titles. Verified manually below.

4. **(Branch state, see top.)** This PR also brings `main` up to the current head.

---

## Verification

```
vendor/bin/pint --test       # passes
npm run build                # passes
php artisan migrate:fresh --seed   # all 75 tables apply cleanly
# Smoke test:
curl -c c.txt -X POST /login   # 302 → /admin/dashboard
curl -b c.txt /admin/notifications        # 200
curl -b c.txt /admin/login-histories      # 200
curl -b c.txt /admin/stock-alerts         # 200
curl -b c.txt /admin/database-backups     # 200
curl -b c.txt /admin/report-exports       # 200
curl -b c.txt /admin/code-sequences       # 200
```

KH/EN switch verified live: Yajra column headers now re-translate without a full reload, and `hr` / `recipes` / `commissions` / `staff_schedules` / `online_orders_module` resolve to their translated strings in both locales.
