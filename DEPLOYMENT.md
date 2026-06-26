# Lake View Sweets & Bakery - cPanel Deployment Guide

## Step 1: cPanel Git Version Control

1. Login to cPanel: https://lakeview-cafe.com:2083
   - Username: lakeviex
   - Password: @D4uYhy@hNl2jo^

2. Go to **Git Version Control** (under Development section)

3. Click **Create** and fill in:
   - Repository Name: `lakeview`
   - Clone URL: `https://github.com/mirjavedjeetu001/lakeview-online.git`
   - Branch: `prod`
   - Repository Path: `/home/lakeviex/lakeview`
   - Click **Create**

4. Wait for the clone to complete

## Step 2: Create .env File

1. Go to **File Manager** in cPanel
2. Navigate to `/home/lakeviex/lakeview/`
3. Create a new file named `.env` with this content:

```
APP_NAME="Lake View Sweets & Bakery"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://lakeview-cafe.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lakeviex_lakeviewonline
DB_USERNAME=lakeviex_lakeviewonline
DB_PASSWORD=JavedMir41@

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER=log
```

4. Save the file

## Step 3: Generate App Key

1. Go to **Terminal** in cPanel (under Advanced section)
2. Run these commands:
```bash
cd /home/lakeviex/lakeview
php artisan key:generate --force
```

## Step 4: Import Database

1. Go to **phpMyAdmin** in cPanel
2. Select database `lakeviex_lakeviewonline`
3. Click **Import** tab
4. Choose file: `/home/lakeviex/lakeview/database/dump.sql`
5. Click **Go** to import

## Step 5: Run Migrations

In cPanel **Terminal**:
```bash
cd /home/lakeviex/lakeview
php artisan migrate --force
```

## Step 6: Set Permissions

In cPanel **Terminal**:
```bash
cd /home/lakeviex/lakeview
chmod -R 775 storage bootstrap/cache
```

## Step 7: Configure Document Root

1. Go to **Domains** in cPanel
2. Click **Manage** next to `lakeview-cafe.com`
3. Change **Document Root** to: `/home/lakeviex/lakeview/public`
4. Save

## Step 8: Clear Caches

In cPanel **Terminal**:
```bash
cd /home/lakeviex/lakeview
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 9: Set Up Auto-Deploy (GitHub Webhook)

1. Copy the `deploy.php` file to your home directory:
   In cPanel **Terminal**:
   ```bash
   cp /home/lakeviex/lakeview/deploy.php /home/lakeviex/public_html/deploy.php
   ```

2. Go to GitHub repo settings:
   https://github.com/mirjavedjeetu001/lakeview-online/settings/hooks

3. Click **Add webhook**:
   - URL: `https://lakeview-cafe.com/deploy.php`
   - Content type: `application/json`
   - Secret: `lakeview_deploy_secret_2024`
   - Events: Just the push event
   - Active: checked
   - Click **Add webhook**

## Auto-Deploy Workflow

From now on, to deploy changes:
1. Make changes on `main` branch
2. Push to GitHub: `git push origin main`
3. Merge to prod: `git checkout prod && git merge main && git push origin prod`
4. GitHub webhook triggers `deploy.php` on server
5. Server pulls prod branch and rebuilds automatically

## Important Notes

- The `vendor/` and `public/build/` directories are included in the repo
  because shared hosting doesn't have composer/npm
- After code changes that affect frontend, rebuild locally:
  ```bash
  npm run build
  git add public/build/
  git commit -m "rebuild assets"
  ```
- After composer dependency changes, install locally:
  ```bash
  composer install --no-dev --optimize-autoloader
  git add vendor/
  git commit -m "update vendor"
  ```
