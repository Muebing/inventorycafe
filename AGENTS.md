# InventoryCafe — Agent Guide

## Stack

- **Laravel 13** on PHP 8.3+, **MySQL** (default), Bootstrap 5 + custom CSS
- Queue, cache, session default to `database` driver
- All assets via CDN (Bootstrap, Bootstrap Icons, Chart.js) — no Vite build needed for styling

## Quick start

```bash
composer setup            # install, .env, key:generate, migrate, seed, npm build
npm run dev               # Vite dev server
composer run dev          # concurrently: server + queue:listen + pail logs + Vite
```

## Testing

```bash
composer test             # config:clear then php artisan test
php artisan test
php artisan test --filter=ExampleTest
```

Tests use SQLite `:memory:` (see `phpunit.xml`). No external services.

## Commands

| Command | Purpose |
|---|---|
| `php artisan serve` | Dev server |
| `php artisan migrate:fresh --seed` | Reset DB + seed sample data |
| `php artisan queue:listen --tries=1 --timeout=0` | Process jobs |
| `php artisan pail` | Live log viewer |
| `./vendor/bin/pint` | Laravel Pint (PSR-12 code style) |

## Auth

- **Manual auth** (no Breeze/Jetstream). Login at `/login`
- Only 1 role: `admin` (stored in `users.role` column)
- Seeded admin: `admin@inventorycafe.test` / `password`

## Project structure

```
app/
├── Http/
│   ├── Controllers/    # AuthController, DashboardController, resource controllers
│   ├── Middleware/      # AdminMiddleware
│   └── Requests/        # Form Request validation classes
├── Models/              # User, Category, Supplier, Item, StockIn, StockOut, StockAdjustment, ActivityLog
└── Services/            # Business logic layer (CategoryService, ItemService, etc.)
database/
├── migrations/          # 11 migration files (users + role + 7 feature tables)
└── seeders/             # DatabaseSeeder creates admin user + sample data
resources/views/
├── layouts/app.blade.php # Custom layout (sidebar, topbar)
├── auth/                # Login page
├── dashboard/           # Cards, Chart.js, stock alerts, recent transactions
├── categories/          # CRUD via modals
├── suppliers/           # CRUD via modals
├── items/               # CRUD + search + filter by category
├── stock_in/            # With filter by date/item
├── stock_out/           # With filter by date/item/tujuan
├── stock_adjustments/   # With filter by date/item
└── activity_logs/       # Paginated activity log
routes/web.php           # Auth routes + admin-protected resource routes
```

## Code conventions

- **Models** use PHP 8 attribute syntax (`#[Fillable]`, `#[Hidden]`) — see `User.php`
- **Controllers** inject Service classes via constructor DI
- **Service classes** handle business logic, ActivityLogService auto-logs all CRUD
- **Database transactions** used in StockIn/StockOut/StockAdjustment services
- **Form Requests** handle validation with custom messages
- Routes use explicit `prefix` + `name` for each resource group

## Key architecture decisions

- Stock In → increments `items.stok`, Stock Out → decrements (validates sufficient stock)
- Stock Adjustment allows positive (add) or negative (subtract) values
- ActivityLog logs `create`/`update`/`delete` with description, model type, and user
- Dashboard shows low stock alert, Chart.js line chart (6-month trend), recent transactions
- All CRUD uses Bootstrap modals (no separate edit pages)
- Delete on stock transactions reverses the stock change (via DB transaction)

## Seeder

`php artisan db:seed` creates:
- 1 admin user + 4 categories + 4 suppliers + 9 sample items
- Items include low-stock and out-of-stock examples for dashboard testing

## Relationship map

```
Category ──hasMany──> Item
Supplier ──hasMany──> StockIn
Item ────hasMany──> StockIn, StockOut, StockAdjustment
User ────hasMany──> ActivityLog
```
