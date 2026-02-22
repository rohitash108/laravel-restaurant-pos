# POS / Restaurant – Setup Guide (MySQL, Dynamic Theme)

This project uses **MySQL** as the database. Follow these steps to run the app with a fully dynamic theme.

---

## Step 1: Create MySQL database

1. Open MySQL (command line, phpMyAdmin, or MySQL Workbench).
2. Create a new database:

```sql
CREATE DATABASE cspos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

You can use another name (e.g. `pos_restaurant`); if so, use it in Step 2.

---

## Step 2: Configure environment

1. Copy the example env file:

```bash
cp .env.example .env
```

2. Generate the application key:

```bash
php artisan key:generate
```

3. Edit `.env` and set your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cspos
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

Use the database name you created in Step 1. Set `DB_USERNAME` and `DB_PASSWORD` to your MySQL user.

---

## Step 3: Install dependencies

```bash
composer install
```

---

## Step 4: Run migrations (create tables in MySQL)

This creates all tables in your MySQL database:

```bash
php artisan migrate
```

Tables created:

- `users` (with role, restaurant_id)
- `restaurants`
- `restaurant_tables`
- `categories`
- `items`
- `orders`
- `order_items`
- `cache`, `jobs`, `sessions`, `password_reset_tokens`

---

## Step 5: Seed initial data (optional)

Creates a super admin, one restaurant, and sample data:

```bash
php artisan db:seed
```

**Seeded accounts:**

| Role             | Email                 | Password  |
|------------------|-----------------------|-----------|
| Super Admin      | admin@example.com     | password  |
| Restaurant Admin | restaurant@example.com| password  |

---

## Step 6: Run the application

```bash
php artisan serve
```

Open: **http://127.0.0.1:8000**

- Login: http://127.0.0.1:8000/login  
- Super Admin → Restaurants: http://127.0.0.1:8000/admin/restaurants  

---

## Dynamic pages (theme + MySQL)

All data comes from the MySQL database; no static content for these:

| Page / Area           | Route / URL              | Data source                          |
|-----------------------|---------------------------|--------------------------------------|
| **Dashboard**         | `/`, `/index`             | Orders count, total sales, averages  |
| **Super Admin – Restaurants** | `/admin/restaurants` | `restaurants` table (only created)   |
| **Restaurant – Tables** | `/table`                | `restaurant_tables` (current restaurant) |
| **Categories**        | `/categories`             | `categories` + item count            |
| **Items (products)**  | `/items`                  | `items` + category, restaurant       |
| **Order by QR (public)** | `/order/{slug}/{table}` | Menu: categories + items; place order → `orders` |

- **Super Admin** sees only restaurants they created (no static list).  
- **Restaurant admin/staff** see only their restaurant’s tables, categories, and items.  
- **Dashboard** stats are from `orders` (and reservations when you add that table).

---

## Quick checklist

- [ ] MySQL server running  
- [ ] Database created (e.g. `cspos`)  
- [ ] `.env` has `DB_CONNECTION=mysql` and correct `DB_*` values  
- [ ] `composer install`  
- [ ] `php artisan key:generate`  
- [ ] `php artisan migrate`  
- [ ] `php artisan db:seed` (optional)  
- [ ] `php artisan serve`  

After this, the project is running on **MySQL** and the theme is driven by the database step by step as described above.
