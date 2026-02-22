# csPos – Dynamic Laravel POS Implementation

This document describes how the static UI theme has been converted into a **fully dynamic, database-driven Laravel application** with MySQL, full CRUD, role-based access, and QR code ordering.

---

## Setup

1. **Environment**
   - Copy `.env.example` to `.env` and set `APP_KEY`, `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

2. **Migrations**
   ```bash
   php artisan migrate
   ```

3. **Seed data** (Super Admin, Restaurant Owner, sample restaurant, categories, products, tables, sample order)
   ```bash
   php artisan db:seed
   ```

4. **Login**
   - **Super Admin:** `admin@example.com` / `password` → redirects to Admin → Restaurants.
   - **Restaurant Owner:** `restaurant@example.com` / `password` → redirects to Dashboard (restaurant context).

---

## Role-Based Access

| Role               | Value in DB       | Access |
|--------------------|-------------------|--------|
| **Super Admin**    | `super_admin`     | Manages **restaurants only** (create, edit, delete, view). Redirected to `/admin/restaurants` when hitting app routes. |
| **Restaurant Owner** | `restaurant_admin` | Manages **products and menus** for their restaurant: categories, items (products), tables, orders, reservations, addons, coupons, customers, POS, kitchen, reports. Cannot access other restaurants. |
| Staff              | `staff`           | Same as Restaurant Owner (restaurant-scoped). |

- **Middleware:** `super_admin` → Admin only; `restaurant` → sets current restaurant from user; `redirect_super_admin_to_admin` → sends Super Admin to admin when visiting app routes.

---

## Full CRUD (Database-Driven)

All data is loaded from MySQL. No hardcoded listings.

### Restaurants (Super Admin only)

- **Routes:** `/admin/restaurants` (index, create, store, edit, update, destroy, show).
- **Controller:** `App\Http\Controllers\Admin\RestaurantsController`.
- **Model:** `App\Models\Restaurant`. Create restaurant can optionally create a Restaurant Owner user.

### Categories (Restaurant Owner / Staff)

- **Routes:** `/categories` (index), `POST /categories` (store), `PUT /categories/{category}` (update), `DELETE /categories/{category}` (destroy).
- **Controller:** `CategoryController`. Uses `ResolvesRestaurant`; data scoped to current restaurant.

### Products / Items (Restaurant Owner / Staff)

- **Routes:** `/items` (index), `POST /items` (store), `PUT /items/{item}` (update), `DELETE /items/{item}` (destroy).
- **Controller:** `ItemController`. Categories and items loaded from DB; forms use dynamic dropdowns.

### Tables (Restaurant Owner / Staff)

- **Routes:** `/table` (index), `POST /table` (store), `PUT /table/{table}` (update), `DELETE /table/{table}` (destroy).
- **Controller:** `RestaurantTableController`. Tables and restaurant context from DB.

### Orders

- **List & status:** `/orders` (index), `PATCH /orders/{order}/status` (update status).
- **Create:**
  - **QR ordering:** Customer scans QR → menu page → place order → `POST /order/place` (see below).
  - **POS:** `POST /orders` → `OrderController@store` (table, order_type, items[], customer_name, notes). Use for dine-in, takeaway, delivery from POS.
- **Controller:** `OrderController`. All order data from DB (recent orders, counts, status updates).

### Other modules (Restaurant-scoped, dynamic)

- **Reservations:** List from `reservations` table; add via `POST /reservations`.
- **Addons:** List from `addons` table; add via `POST /addons`.
- **Coupons:** List from `coupons` table; add via `POST /coupons`.
- **Customers:** List from `customers` + orders; add via `POST /customer`.

---

## QR Code Ordering System

1. **QR code URL** (per table):  
   `/order/{restaurant:slug}/{table}`  
   e.g. `/order/taste-of-lhasa/table-1`

2. **Generate QR image:**  
   `GET /order/{restaurant:slug}/{table}/qr`  
   Returns PNG/SVG of QR code linking to the menu URL.

3. **Menu page (public, no login):**  
   `GET /order/{restaurant:slug}/{table}`  
   - Loads restaurant, table, and categories with active items from DB.  
   - Customer adds items to cart and submits.

4. **Place order:**  
   `POST /order/place`  
   - Body: `restaurant_id`, `restaurant_table_id`, `items[]` (item_id, quantity), optional `customer_name`, `notes`.  
   - Creates `Order` + `OrderItem` records; redirects to success page with order number.

5. **Success page:**  
   `GET /order/{restaurant:slug}/{table}/success/{order}`  
   Shows order number and short instructions.

- **Controller:** `OrderByQRController` (`qrImage`, `show`, `placeOrder`, `success`). Menu and orders are fully dynamic from the database.

---

## Dashboards & Listings

- **Dashboard (`/index`):** Total orders, total sales, average order value, reservations count, recent orders, top selling items, tables – all from DB. Reservations from `reservations` table when present.
- **POS (`/pos`):** Categories, items, tables, recent orders – from DB. Place order can POST to `orders.store`.
- **Orders, Kitchen, Kanban, Invoices, Reports:** All use controller-provided data (orders, statuses, totals). No static rows.

---

## Laravel Conventions Used

- **MVC:** Models (`App\Models\*`), Controllers (`App\Http\Controllers\*`), Views (`resources/views/*`).
- **Migrations:** All tables defined in `database/migrations` (users, restaurants, restaurant_tables, categories, items, orders, order_items, customers, reservations, addons, coupons).
- **Seeders:** `DatabaseSeeder` for Super Admin, Restaurant Owner, one restaurant, categories, items, tables, sample order.
- **Validation:** In controller `store`/`update` methods via `$request->validate()`.
- **Authentication:** Session-based login (`CustomAuthController`); roles stored on `users` table (`role`, `restaurant_id`).
- **Authorization:** Restaurant-scoped controllers use `ResolvesRestaurant` and abort 403 when resource does not belong to current restaurant; Super Admin restricted to admin routes.

---

## Summary

- **Static data removed:** Listings, forms, and dashboards load from MySQL.
- **Full CRUD:** Restaurants (admin), Categories, Products/Items, Tables, Orders (create via QR and POS; read/update status in app).
- **Roles:** Super Admin (restaurants only); Restaurant Owner (products and menus for their restaurant).
- **QR ordering:** Scan QR → view menu (DB) → place order → order stored in DB.
- **Best practices:** MVC, migrations, seeders, validation, authentication, role-based middleware.
