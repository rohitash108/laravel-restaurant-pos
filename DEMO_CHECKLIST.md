# Demo Checklist – Client Walkthrough

Use this list to test every module step by step before and during the client demo.

---

## Before the demo

1. **Run automated tests**
   ```bash
   php artisan test
   ```
   All tests should pass (**14 tests**), including:
   - **FullFlowE2ETest**: Super Admin dashboard → create restaurant (with admin user) → restaurant user adds category, item, table → POS order (dine-in) → complete order → all reports + all restaurant modules → reservation → category/item update → logout.
   - **restaurant user can place takeaway order without table**: POS takeaway order path.
   - **ModulesTest**: Auth, guest redirects, all module GET access, invoice details, Order-by-QR.

2. **Seed data (if needed)**
   ```bash
   php artisan migrate --seed
   ```
   This creates:
   - **Super Admin:** `admin@example.com` / `password`
   - **Restaurant user:** `restaurant@example.com` / `password`
   - One restaurant (Taste of Lhasa), tables, sample categories/items, one sample order.

3. **Start the app**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` (or your configured URL).

---

## 1. Auth & access

- [ ] **Login (restaurant user)**  
  Go to `/login` → email `restaurant@example.com`, password `password` → should land on **Dashboard**.
- [ ] **Logout**  
  Profile menu → Logout → should land on Login.
- [ ] **Login (super admin)**  
  Email `admin@example.com`, password `password` → should land on **Super Admin Dashboard** (`/admin/dashboard`), not the main dashboard.
- [ ] **Register**  
  Open `/register` → page loads (form works if registration is enabled).

---

## 2. Restaurant user – main app

Log in as **restaurant@example.com**.

### Dashboard
- [ ] Dashboard loads with KPIs (Total Orders, Total Sales, Average Value, Reservations).
- [ ] Charts and “Top Selling Item”, “Recent Orders”, “Reservations”, “Tables” sections load without errors.

### POS
- [ ] **POS** (`/pos`) loads.
- [ ] Select table (or walk-in), add items from menu, place order (or save as pending).
- [ ] Total updates; order appears in Orders.

### Orders
- [ ] **Orders** (`/orders`) lists orders; tabs (All, Pending, In Progress, Completed, Cancelled) work.
- [ ] Change order status (e.g. Pending → In Progress → Completed).
- [ ] Open an order; edit if applicable.

### Kitchen
- [ ] **Kitchen** (`/kitchen`) loads and shows active orders (if any).

### Tables
- [ ] **Table** (`/table`) lists tables; add / edit / delete table (name, floor, capacity).

### Reservations
- [ ] **Reservations** (`/reservations`) lists reservations; add new reservation; edit / cancel one.

### Menu & catalog
- [ ] **Items** (`/items`) – list, add, edit, hide item; set price, category.
- [ ] **Categories** (`/categories`) – list, add, edit, delete category.
- [ ] **Addons** (`/addons`) – list, add, edit, delete addon.

### Customers
- [ ] **Customer** (`/customer`) – list and manage customers (if used).

### Reports
- [ ] **Earning Report** (`/earning-report`) – loads; optional date filter.
- [ ] **Sales Report** (`/sales-report`) – loads.
- [ ] **Order Report** (`/order-report`) – loads.
- [ ] **Customer Report** (`/customer-report`) – loads.
- [ ] **Audit Logs** (`/audit-report`) – loads.

### Invoices & payments
- [ ] **Invoices** (`/invoices`) – list of orders; open **Invoice details** for one order.
- [ ] **Payments** (`/payments`) – page loads.

### Settings
- [ ] **Store Settings** – view/save.
- [ ] **Tax Settings** – list, add, edit, delete tax.
- [ ] **Payment / Delivery / Print / Notifications / Integrations** – pages load and save if used.

### Staff & roles
- [ ] **Users** (`/users`) – list, add, edit staff; assign restaurant/role.
- [ ] **Roles & Permissions** (`/role-permission`) – list, add, edit roles.

### Other
- [ ] **Coupons** (`/coupons`) – list, add, edit.
- [ ] **Kanban View** (`/kanban-view`) – loads (order board if used).

---

## 3. Super Admin

Log in as **admin@example.com**.

- [ ] **Super Admin Dashboard** (`/admin/dashboard`) – KPIs (restaurants, orders, sales, growth), chart, Quick actions, Recent Restaurants.
- [ ] **Restaurants** – list all restaurants; **Add Restaurant**; **Edit** one; **View** one (detail page).
- [ ] Sidebar and top bar show only admin links (no POS/Orders/Kitchen for super admin).
- [ ] Logo links to Super Admin Dashboard.

---

## 4. Order by QR (public, no login)

- [ ] Open: `/order/taste-of-lhasa/table-1` (or your restaurant slug and table slug).
- [ ] Menu page loads with categories and items.
- [ ] Add items, enter name, place order.
- [ ] Success page or confirmation appears.

---

## Step-by-step flow (what the E2E test does)

The **FullFlowE2ETest** runs this exact sequence:

1. **Super Admin** – Login → Admin Dashboard (200) → Create restaurant with admin user (name, slug, admin email/password) → Restaurants list → Restaurant show → Restaurant edit.
2. **Restaurant user** – Logout super admin, login as new restaurant admin → Dashboard (200).
3. **Catalog** – Add category (Main Course) → Add item (Grilled Chicken, price, category) → Add table (T1, floor, capacity).
4. **POS** – Place dine-in order (table T1, 2× Grilled Chicken) → redirect to POS with success.
5. **Orders** – Mark order completed (status update).
6. **Reports** – Earning, Sales, Order, Customer, Audit (all 200).
7. **Invoices** – Invoices list → Invoice details for the order (200).
8. **All modules** – POS, Orders, Kitchen, Table, Reservations, Items, Categories, Addons, Customer, Payments, Coupons, Kanban, Users, Role & Permission, Store/Tax/Payment/Delivery/Print/Notifications/Integrations settings (all 200).
9. **Reservation** – Create reservation (customer, date, time, table, guests, status).
10. **Updates** – Category name update → Item price update → success.
11. **Logout** – Redirect to login.

A second test verifies **takeaway order** (no table) from POS.

---

## Quick test command

From project root:

```bash
php artisan test
```

All **14 tests** should pass. This covers:

- **Full E2E**: Super Admin creates restaurant with admin user → restaurant user adds category, item, table → POS dine-in order → complete order → all reports and modules → reservation → category/item update → logout  
- **Takeaway order**: Restaurant user places POS order without table  
- Guest redirect to login for protected routes  
- Login page  
- Restaurant user login → dashboard  
- Restaurant user access to all main modules (POS, Orders, Kitchen, Tables, Reservations, Items, Categories, Addons, Customers, Reports, Invoices, Users, Settings, etc.)  
- Super admin login → admin dashboard  
- Super admin access to admin dashboard and restaurants (list, create, show, edit)  
- Public Order-by-QR page  
- Register page  
- Logout  
- Invoice details for own restaurant order  

---

## If something fails

- Check `.env` (APP_URL, DB_*, session driver).
- Run `php artisan config:clear` and `php artisan cache:clear`.
- For 500 errors, check `storage/logs/laravel.log`.
- For “Undefined variable” in views, ensure the controller passes the required data (or it’s shared in `AppServiceProvider`).
