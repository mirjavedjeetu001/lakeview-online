# Lake View Sweets & Bakery - cPanel Deployment Guide (No Terminal)

## Step 1: Clone Repo via cPanel Git Version Control

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
3. Click **+ File** and create a new file named `.env`
4. Edit it and paste this content:

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

5. Save the file

## Step 3: Set Permissions

1. In **File Manager**, navigate to `/home/lakeviex/lakeview/`
2. Right-click on `storage` folder → **Change Permissions** → set to `775`
3. Right-click on `bootstrap/cache` folder → **Change Permissions** → set to `775`

## Step 4: Import Database

1. Go to **phpMyAdmin** in cPanel
2. Select database `lakeviex_lakeviewonline` (left sidebar)
3. Click **Import** tab
4. Click **Choose File** and select the `dump.sql` file:
   - Navigate to `/home/lakeviex/lakeview/database/dump.sql`
5. Click **Go** to import
6. Wait for "Import has been successfully finished" message

## Step 5: Change Document Root

1. Go to **Domains** in cPanel
2. Click **Manage** next to `lakeview-cafe.com`
3. Change **Document Root** to: `/home/lakeviex/lakeview/public`
4. Save

## Step 6: Run Setup Script (Replaces Terminal)

1. Open your browser and visit:
   ```
   https://lakeview-cafe.com/setup.php
   ```
2. This will automatically:
   - Generate the APP_KEY
   - Run database migrations
   - Create storage symlink
   - Cache config, routes, and views

3. You should see "OK" for each step

## Step 7: Delete Setup File (IMPORTANT!)

1. Go back to **File Manager**
2. Navigate to `/home/lakeviex/lakeview/public/`
3. Delete `setup.php` (security risk if left!)

## Step 8: Test Your Website

Visit: **https://lakeview-cafe.com**

Your website should be live!

---

## Auto-Deploy Setup (Optional - For Future Updates)

### Copy deploy script:
1. In **File Manager**, copy `/home/lakeviex/lakeview/deploy.php`
   to `/home/lakeviex/public_html/deploy.php`

### Add GitHub Webhook:
1. Go to: https://github.com/mirjavedjeetu001/lakeview-online/settings/hooks
2. Click **Add webhook**:
   - URL: `https://lakeview-cafe.com/deploy.php`
   - Content type: `application/json`
   - Secret: `lakeview_deploy_secret_2024`
   - Events: Just the push event
   - Active: checked
3. Click **Add webhook**

### Future Deploy Workflow:
```bash
# On your local machine:
git checkout main
# make changes...
git add . && git commit -m "your changes"
git push origin main

# Merge to prod to trigger auto-deploy:
git checkout prod
git merge main
git push origin prod
```

### If Frontend Changes:
```bash
npm run build
git add public/build/
git commit -m "rebuild assets"
git push origin main
git checkout prod && git merge main && git push origin prod
git checkout main
```
