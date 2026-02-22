# Deploy Laravel Restaurant POS on Your Existing AWS EC2 Instance

Use this guide to add this project on the same EC2 instance where another project is already running. The new app will run in its own directory with its own (sub)domain or port.

---

## Prerequisites

- EC2 instance with SSH access
- PHP 8.1+ (and extensions: mbstring, xml, pdo_mysql, etc.), Composer, MySQL/MariaDB
- Nginx or Apache already installed (for your current project)

---

## Step 1: SSH into your EC2 instance

*(Fetched from your shell history – user and IP so you don’t forget.)*

```bash
# Your instance: Ubuntu @ 15.206.125.133
ssh -i cslabs.pem ubuntu@15.206.125.133
```

If the key is in another folder, use the full path:
```bash
ssh -i /full/path/to/cslabs.pem ubuntu@15.206.125.133
```

---

## Step 2: Choose a directory for the new project

Run the new app in a separate folder so it doesn’t conflict with the existing one.

**Option A – Same web root parent (common):**
```bash
sudo mkdir -p /var/www/restaurant-pos
sudo chown $USER:$USER /var/www/restaurant-pos
cd /var/www/restaurant-pos
```

**Option B – Under your existing app’s user:**
```bash
cd ~
mkdir -p restaurant-pos
cd restaurant-pos
```

Use one path consistently in the steps below (we’ll use `/var/www/restaurant-pos` in examples).

---

## Step 3: Clone the GitHub repo

```bash
cd /var/www/restaurant-pos

# If you use HTTPS (will ask for GitHub username/token if private):
git clone https://github.com/rohitash108/laravel-restaurant-pos.git .

# If repo is private and you use a token:
# git clone https://YOUR_GITHUB_TOKEN@github.com/rohitash108/laravel-restaurant-pos.git .

# If you use SSH:
# git clone git@github.com:rohitash108/laravel-restaurant-pos.git .
```

The `.` at the end clones into the current directory so files sit in `/var/www/restaurant-pos`.

---

## Step 4: Install PHP dependencies

```bash
cd /var/www/restaurant-pos

# Install Composer if not installed:
# curl -sS https://getcomposer.org/installer | php
# sudo mv composer.phar /usr/local/bin/composer

composer install --no-dev --optimize-autoloader
```

If you see missing PHP extensions, install them, for example (Amazon Linux 2 / RHEL-style):
```bash
sudo yum install php81-mbstring php81-xml php81-mysqlnd php81-pdo php81-tokenizer php81-openssl php81-fileinfo
# Or for Ubuntu:
# sudo apt install php-mbstring php-xml php-mysql php-zip php-curl
```

---

## Step 5: Environment and app key

```bash
cd /var/www/restaurant-pos

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your production values:

```bash
nano .env
```

Set at least:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain-for-pos.com` (or your subdomain, e.g. `https://pos.yourdomain.com`)
- `DB_DATABASE=restaurant_pos` (or a name you will create in Step 6)
- `DB_USERNAME=your_db_user`
- `DB_PASSWORD=your_db_password`
- `DB_HOST=127.0.0.1` (or your RDS/MySQL host if different)

Save and exit (in nano: Ctrl+O, Enter, Ctrl+X).

---

## Step 6: Create database and run migrations

**Create a dedicated database for this app:**

```bash
mysql -u root -p
```

In MySQL:

```sql
CREATE DATABASE restaurant_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'restaurant_pos_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON restaurant_pos.* TO 'restaurant_pos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Use the same database name, user, and password in `.env`, then:

```bash
cd /var/www/restaurant-pos
php artisan migrate --force
```

---

## Step 7: Storage link and permissions

```bash
cd /var/www/restaurant-pos

php artisan storage:link

sudo chown -R www-data:www-data storage bootstrap/cache
# Amazon Linux 2 often uses 'apache' or 'nginx' instead of 'www-data':
# sudo chown -R apache:apache storage bootstrap/cache

chmod -R 775 storage bootstrap/cache
```

---

## Step 8: Web server configuration

Serve the app through the **document root** `public` inside the project. Use a new server block (Nginx) or VirtualHost (Apache) so it doesn’t affect your existing project.

### Nginx (new site)

Create a new config file (name it after your domain):

```bash
sudo nano /etc/nginx/conf.d/restaurant-pos.conf
```

Paste (replace `your-domain.com` and path if you chose a different directory):

```nginx
server {
    listen 80;
    server_name your-domain.com;   # or pos.yourdomain.com

    root /var/www/restaurant-pos/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;   # or 127.0.0.1:9000
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

Test and reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Apache (new VirtualHost)

```bash
sudo nano /etc/httpd/conf.d/restaurant-pos.conf
# Or on Ubuntu: sudo nano /etc/apache2/sites-available/restaurant-pos.conf
```

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/restaurant-pos/public

    <Directory /var/www/restaurant-pos/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Enable and reload (Ubuntu example):

```bash
# Ubuntu:
# sudo a2ensite restaurant-pos
sudo systemctl reload apache2
# Amazon Linux 2:
# sudo systemctl reload httpd
```

---

## Step 9: Point domain to this server

In your DNS (Route 53 or your registrar):

- Add an **A record** for `your-domain.com` (or `pos.yourdomain.com`) pointing to your EC2 **public IP** (or Elastic IP).

After DNS propagates, open `http://your-domain.com` in the browser. You should see the Laravel app.

---

## Step 10: Optional – SSL (HTTPS)

Using Certbot (Let’s Encrypt):

```bash
sudo certbot --nginx -d your-domain.com
# Or for Apache: sudo certbot --apache -d your-domain.com
```

Then set in `.env`: `APP_URL=https://your-domain.com` and restart PHP-FPM if needed.

---

## Step 11: Optional – scheduler and queue (if you use them)

**Cron (for `php artisan schedule:run`):**

```bash
crontab -e
```

Add:

```
* * * * * cd /var/www/restaurant-pos && php artisan schedule:run >> /dev/null 2>&1
```

**Queue worker (if you use queues):**

```bash
cd /var/www/restaurant-pos
php artisan queue:work --daemon
```

For a permanent worker, use Supervisor or a systemd service (same as you might already use for your other project).

---

## Quick checklist

| Step | Action |
|------|--------|
| 1 | SSH to EC2 |
| 2 | Create directory (e.g. `/var/www/restaurant-pos`) |
| 3 | Clone repo into that directory |
| 4 | `composer install --no-dev --optimize-autoloader` |
| 5 | `cp .env.example .env` → edit `.env` → `php artisan key:generate` |
| 6 | Create DB + user, then `php artisan migrate --force` |
| 7 | `php artisan storage:link` + fix ownership/permissions |
| 8 | Add Nginx/Apache site for this app (root = `.../public`) |
| 9 | Point domain A record to EC2 IP |
| 10 | (Optional) SSL with certbot |

---

## Updating the app later

```bash
cd /var/www/restaurant-pos
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Use this guide as your step-by-step to add this project on your existing AWS instance. If you tell me your OS (Amazon Linux 2 / Ubuntu) and web server (Nginx/Apache), I can narrow the commands exactly to your setup.
