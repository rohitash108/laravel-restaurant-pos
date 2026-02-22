# Steps 7, 8, 9 – Nginx, DNS, HTTPS & Cron (Ubuntu)

Use this after you’ve completed steps 1–6. Replace `pos.yourdomain.com` with your real subdomain/domain.

**Your EC2 public IP:** `15.206.125.133`

---

## Step 7: Web server (Nginx)

### 7.1 Check if you use Nginx or Apache

```bash
sudo systemctl status nginx
# or
sudo systemctl status apache2
```

Use the one that’s **active**. Below is for **Nginx**. (Apache version is at the end.)

### 7.2 Find PHP-FPM socket (Nginx needs this)

```bash
ls /var/run/php/php*.sock
```

You’ll see something like `/var/run/php/php8.4-fpm.sock`. Use that path in the config below.

### 7.3 Create Nginx server block

**Ubuntu:** config in `sites-available`, then enable.

```bash
sudo nano /etc/nginx/sites-available/restaurant-pos
```

Paste this (replace `pos.yourdomain.com` and fix `fastcgi_pass` if your PHP socket path is different):

```nginx
server {
    listen 80;
    server_name pos.yourdomain.com;

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
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
```

Save (Ctrl+O, Enter, Ctrl+X).

### 7.4 Enable the site and reload Nginx

```bash
sudo ln -s /etc/nginx/sites-available/restaurant-pos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

If `nginx -t` fails, fix the path or socket name it reports.

---

## Step 8: DNS – point domain to EC2

1. Go to where your domain is managed (e.g. **Route 53**, GoDaddy, Cloudflare, etc.).
2. Add an **A record**:
   - **Name:** `pos` (for `pos.yourdomain.com`) or `@` (for `yourdomain.com`).
   - **Type:** `A`
   - **Value:** `15.206.125.133`
   - **TTL:** 300 (or default).

3. Wait 5–15 minutes (or longer) for DNS to propagate.
4. Test: `ping pos.yourdomain.com` – should show `15.206.125.133`.

Then open in browser: `http://pos.yourdomain.com` – you should see the Laravel app.

---

## Step 9 (optional)

### 9.1 HTTPS with Let’s Encrypt

```bash
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d pos.yourdomain.com
```

Follow prompts (email, agree). Certbot will edit Nginx and add SSL.

Then in Laravel `.env`:

```env
APP_URL=https://pos.yourdomain.com
```

Clear config:

```bash
cd /var/www/restaurant-pos
php artisan config:clear
```

### 9.2 Cron for Laravel scheduler

```bash
crontab -e
```

Pick an editor, then add this line (one line):

```
* * * * * cd /var/www/restaurant-pos && php artisan schedule:run >> /dev/null 2>&1
```

Save and exit. Laravel’s scheduler will run every minute.

---

## If you use Apache instead of Nginx

```bash
sudo nano /etc/apache2/sites-available/restaurant-pos.conf
```

```apache
<VirtualHost *:80>
    ServerName pos.yourdomain.com
    DocumentRoot /var/www/restaurant-pos/public

    <Directory /var/www/restaurant-pos/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Then:

```bash
sudo a2ensite restaurant-pos
sudo a2enmod rewrite
sudo systemctl reload apache2
```

DNS (Step 8) and optional Step 9 are the same; for HTTPS use:

```bash
sudo certbot --apache -d pos.yourdomain.com
```

---

## Quick checklist

| Step | Done |
|------|------|
| 7.1  | Nginx (or Apache) running |
| 7.2  | PHP-FPM socket path noted |
| 7.3  | Server block created, `server_name` and `root` correct |
| 7.4  | Site enabled, `nginx -t` OK, Nginx reloaded |
| 8    | A record for domain → 15.206.125.133 |
| 9.1  | (Optional) certbot HTTPS |
| 9.2  | (Optional) crontab for `schedule:run` |
