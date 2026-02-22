# .env Setup Guide – Step Order & What to Put

## Order of steps (recommended)

**Yes, create the database first**, then copy and edit `.env`. That way your `.env` can point to a real database when you run migrations.

1. Create the database and user (see below).
2. `cp .env.example .env`
3. `php artisan key:generate`
4. Edit `.env` and set all values (especially DB_* and APP_*).
5. Run `php artisan migrate` (this needs the DB to exist).

---

## Step 1: Create the database first (on your server)

SSH into your EC2/server and run:

```bash
# Log into MySQL as root (use the password you set when MySQL was installed)
sudo mysql -u root -p
```

In the MySQL prompt, run (change database name, username, and password if you want):

```sql
-- Create a dedicated database for Restaurant POS
CREATE DATABASE restaurant_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create a user (only for this app)
CREATE USER 'restaurant_pos_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';

-- Give that user full access to this database only
GRANT ALL PRIVILEGES ON restaurant_pos.* TO 'restaurant_pos_user'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;

-- Exit
EXIT;
```

Remember:
- **Database name:** `restaurant_pos` (or whatever you used in `CREATE DATABASE`)
- **Username:** `restaurant_pos_user`
- **Password:** `YourStrongPassword123!`

You will put these **exact** values in `.env` in the next step.

---

## Step 2 & 3: Copy .env and generate key

```bash
cd /var/www/restaurant-pos   # or your project path

cp .env.example .env
php artisan key:generate
```

`key:generate` fills `APP_KEY` in `.env`. Do not change it manually.

---

## Step 4: What to put in the .env file (detailed)

Run `nano .env` and set these. **Must change** = required for production.

### App basics (must change for production)

| Variable    | Example value              | What it means |
|------------|----------------------------|----------------|
| `APP_NAME` | `Restaurant POS`           | App name (shown in UI, emails). |
| `APP_ENV`  | `production`               | **Must be `production`** on live server. |
| `APP_KEY`  | *(already set by key:generate)* | Encryption key. Don’t edit. |
| `APP_DEBUG`| `false`                    | **Must be `false`** in production (no error details to users). |
| `APP_URL`  | `https://pos.yourdomain.com`| Full URL where the app is accessed. No trailing slash. |

So at the top of `.env` you should have something like:

```env
APP_NAME="Restaurant POS"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
APP_DEBUG=false
APP_URL=https://pos.yourdomain.com
```

---

### Database (must match the DB you created)

These **must** match the database and user you created in Step 1:

| Variable       | Example value          | What it means |
|----------------|------------------------|----------------|
| `DB_CONNECTION`| `mysql`                | Keep as `mysql`. |
| `DB_HOST`      | `127.0.0.1`            | Where MySQL runs. Same server = `127.0.0.1`. RDS = RDS endpoint. |
| `DB_PORT`      | `3306`                 | MySQL port (default 3306). |
| `DB_DATABASE`  | `restaurant_pos`       | **Exact** name from `CREATE DATABASE`. |
| `DB_USERNAME`  | `restaurant_pos_user`  | **Exact** user from `CREATE USER`. |
| `DB_PASSWORD`  | `YourStrongPassword123!` | **Exact** password from `IDENTIFIED BY`. |

Example block:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_pos
DB_USERNAME=restaurant_pos_user
DB_PASSWORD=YourStrongPassword123!
```

If the DB is on another host (e.g. AWS RDS), set `DB_HOST` to that hostname.

---

### Logging (recommended for production)

| Variable   | Production value | What it means |
|-----------|-------------------|----------------|
| `LOG_CHANNEL` | `stack`        | Keep. |
| `LOG_LEVEL`   | `error` or `warning` | Less disk I/O than `debug`. |

```env
LOG_CHANNEL=stack
LOG_LEVEL=error
```

---

### Session, cache, queue (can leave as-is)

This project uses the database for sessions and cache. No need to change if DB is correct:

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

### Mail (optional for now)

For production you can keep logging to file, or configure real mail later:

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

### AWS (only if you use S3 for storage)

If you’re not using S3, leave empty or as-is:

```env
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
```

---

### What you can leave as in .env.example

- `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`
- `BCRYPT_ROUNDS`
- `SESSION_*` (except if you use a different domain)
- `FILESYSTEM_DISK=local`
- Redis/Memcached if you’re not using them

---

## Step 5: Test DB connection, then migrate

```bash
# Test that Laravel can see the DB (optional)
php artisan db:show

# Create tables
php artisan migrate --force
```

If `db:show` or `migrate` fails, double-check `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and that the database and user were created in Step 1.

---

## Quick checklist

- [ ] Database created (`restaurant_pos` or your name).
- [ ] DB user created with password and `GRANT` on that database.
- [ ] `cp .env.example .env` and `php artisan key:generate`.
- [ ] `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=...`.
- [ ] `.env`: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` match the DB you created.
- [ ] `php artisan migrate --force` runs without errors.

After this, your app can connect to the database and use it for sessions, cache, and data.
