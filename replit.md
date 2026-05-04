# Workspace

## Overview

pnpm workspace monorepo using TypeScript, plus a standalone Laravel-based WMS application.

---

## WMS — Warehouse Management System

A full-featured, production-grade WMS built with **Laravel 11 + MySQL + Tailwind CSS v3 + jQuery**.

### Location
`/home/runner/workspace/wms/`

### Access
- **URL**: Port 3000 (select "WMS - Warehouse Management" from the workflow dropdown)
- **Login**: admin@wms.com / password
- **Other users**: manager@wms.com / password, staff@wms.com / password

### Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL (socket: `/tmp/mysql.sock`, DB: `wms`, port: 3307)
- **Frontend**: Tailwind CSS v3 + jQuery 3.7 + Chart.js (via Vite)
- **Auth**: Laravel Sanctum sessions + Spatie Permissions (roles: Admin, Manager, Staff)

### Modules
- Dashboard (summary cards, revenue charts, top vendors/customers, low stock alerts)
- Items (inventory items, categories, brands, units, variants, image upload)
- Purchases (PO creation, receive stock, payments/due tracking)
- Sales (invoice creation, PDF-ready invoice view, payments/due tracking)
- Warehouses (multi-warehouse support, manager assignment)
- Suppliers (create/edit/show, purchase history, balance sheet)
- Customers (create/edit/show, sales history, balance sheet)
- Stock Transfers (warehouse-to-warehouse transfers)
- Inventory Adjustments (quantity corrections with reason)
- Expenses (with categories, expense tracking)
- Reports (Profit & Loss, Sales, Purchases, Stock Value, Open Balance Sheet)
- User Management (roles, active/inactive, password reset)

### Startup
Workflow: `WMS - Warehouse Management` runs `bash /home/runner/workspace/wms/start.sh`
The script:
1. Starts MySQL on port 3307
2. Creates DB/user if missing
3. Runs migrations + seeds (idempotent)
4. Builds frontend assets (Vite)
5. Starts `php artisan serve --host=0.0.0.0 --port=3000`

### Seed Data
- 3 Warehouses, 8 Units, 7 Categories, 6 Brands
- 10 Items with inventory
- 5 Suppliers, 5 Customers
- 8 Expense Categories

---

## Monorepo (TypeScript/Node.js)

### Stack
- **Monorepo tool**: pnpm workspaces
- **Node.js version**: 24
- **Package manager**: pnpm
- **TypeScript version**: 5.9
- **API framework**: Express 5
- **Database**: PostgreSQL + Drizzle ORM
- **Validation**: Zod (`zod/v4`), `drizzle-zod`
- **API codegen**: Orval (from OpenAPI spec)
- **Build**: esbuild (CJS bundle)

### Key Commands
- `pnpm run typecheck` — full typecheck across all packages
- `pnpm run build` — typecheck + build all packages
- `pnpm --filter @workspace/api-spec run codegen` — regenerate API hooks and Zod schemas from OpenAPI spec
- `pnpm --filter @workspace/db run push` — push DB schema changes (dev only)
- `pnpm --filter @workspace/api-server run dev` — run API server locally

See the `pnpm-workspace` skill for workspace structure, TypeScript setup, and package details.
