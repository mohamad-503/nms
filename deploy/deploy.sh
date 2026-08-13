#!/bin/bash
set -e

echo "=== NM System Deployment ==="

# Paths
APP_DIR="/var/www/nm-system"
PHP_VERSION="8.3"

# 1. Install system dependencies
echo "[1/8] Installing system packages..."
sudo apt-get update -qq
sudo apt-get install -y -qq nginx mysql-server php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-curl php${PHP_VERSION}-zip php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl composer nodejs npm unzip curl > /dev/null

# 2. MySQL database
echo "[2/8] Setting up MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS nm_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'nm_user'@'localhost' IDENTIFIED BY 'secret';"
sudo mysql -e "GRANT ALL PRIVILEGES ON nm_system.* TO 'nm_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# 3. Copy project files (assumes repo is cloned to $APP_DIR)
echo "[3/8] Installing PHP dependencies..."
cd $APP_DIR
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Environment
echo "[4/8] Configuring environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

# 5. Migrate & seed
echo "[5/8] Running migrations..."
php artisan migrate --force
php artisan db:seed --force

# 6. Storage & permissions
echo "[6/8] Setting permissions..."
php artisan storage:link
sudo chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# 7. Frontend build
echo "[7/8] Building frontend assets..."
npm install --silent
npm run build

# 8. Nginx
echo "[8/8] Configuring Nginx..."
sudo cp deploy/nginx.conf /etc/nginx/sites-available/nm-system
sudo ln -sf /etc/nginx/sites-available/nm-system /etc/nginx/sites-enabled/nm-system
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl restart nginx
sudo systemctl restart php${PHP_VERSION}-fpm

echo "=== Deployment complete ==="
echo "Visit: http://$(hostname -I | awk '{print $1}')"
echo "Login: admin@nm.iq / admin123"
