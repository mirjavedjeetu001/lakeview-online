#!/bin/bash
# Deployment script for cPanel shared hosting
# Run this on the server after pulling the prod branch

set -e

PROJECT_DIR="/home/lakeviewcafe/public_html"
PUBLIC_HTML="/home/lakeviewcafe/public_html"
PHP_BIN="/opt/cpanel/ea-php84/root/usr/bin/php"

echo "=== Lake View Sweets & Bakery Deployment ==="

# Clone/pull the repository
if [ -d "$PROJECT_DIR/.git" ]; then
    echo "Pulling latest code..."
    cd $PROJECT_DIR
    git fetch origin
    git checkout prod
    git pull origin prod
else
    echo "Cloning repository..."
    cd /home/lakeviewcafe
    git clone -b prod https://github.com/mirjavedjeetu001/lakeview-online.git lakeview
    cd $PROJECT_DIR
fi

# Install Composer dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader 2>/dev/null || echo "Composer not available, skipping..."

# Install Node dependencies and build
echo "Building frontend assets..."
npm install 2>/dev/null || echo "npm not available, skipping..."
npm run build 2>/dev/null || echo "npm build not available, skipping..."

# Configure .env
echo "Configuring environment..."
if [ ! -f "$PROJECT_DIR/.env" ]; then
    cp $PROJECT_DIR/.env.example $PROJECT_DIR/.env
    "$PHP_BIN" artisan key:generate --force
fi

# Symlink public_html to project public directory
echo "Linking public_html..."
# Backup existing public_html if it has content
if [ -d "$PUBLIC_HTML" ] && [ ! -L "$PUBLIC_HTML" ]; then
    mv $PUBLIC_HTML ${PUBLIC_HTML}_backup_$(date +%Y%m%d%H%M%S)
fi
ln -sf $PROJECT_DIR/public $PUBLIC_HTML

# Ensure storage permissions
echo "Setting permissions..."
chmod -R 775 $PROJECT_DIR/storage
chmod -R 775 $PROJECT_DIR/bootstrap/cache

# Run migrations
echo "Running migrations..."
"$PHP_BIN" artisan migrate --force

# Clear caches
echo "Clearing caches..."
"$PHP_BIN" artisan config:clear
"$PHP_BIN" artisan route:clear
"$PHP_BIN" artisan view:clear
"$PHP_BIN" artisan cache:clear

# Optimize for production
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

echo "=== Deployment Complete! ==="
echo "Visit: https://lakeview-cafe.com"
