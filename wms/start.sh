#!/bin/bash
set -e

cd /home/runner/workspace/wms

# ─── 1. Start MySQL ─────────────────────────────────────────────────────────
if [ ! -d /tmp/mysql-data/mysql ]; then
  echo "Initializing MySQL data directory..."
  mysqld --initialize-insecure --datadir=/tmp/mysql-data --user=$(whoami) 2>/dev/null || true
fi

if ! mysqladmin --socket=/tmp/mysql.sock ping --silent 2>/dev/null; then
  echo "Starting MySQL..."
  mysqld --datadir=/tmp/mysql-data \
         --socket=/tmp/mysql.sock \
         --pid-file=/tmp/mysql.pid \
         --port=3307 \
         --daemonize \
         --log-error=/tmp/mysql-error.log 2>/dev/null || true
  sleep 3
fi

# ─── 2. Create DB and user if needed ────────────────────────────────────────
mysql --socket=/tmp/mysql.sock -u root 2>/dev/null <<'SQL' || true
CREATE DATABASE IF NOT EXISTS wms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'wms_user'@'localhost' IDENTIFIED BY 'wms_password_2024';
GRANT ALL PRIVILEGES ON wms.* TO 'wms_user'@'localhost';
FLUSH PRIVILEGES;
SQL

# ─── 3. Install PHP dependencies if needed ───────────────────────────────────
if [ ! -d vendor ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader 2>/dev/null
fi

# ─── 4. Install Node dependencies if needed ──────────────────────────────────
if [ ! -d node_modules ]; then
  echo "Installing node dependencies..."
  npm install 2>/dev/null
fi

# ─── 5. Generate app key if not set ─────────────────────────────────────────
php artisan key:generate --no-interaction 2>/dev/null || true

# ─── 6. Create storage link ──────────────────────────────────────────────────
php artisan storage:link --no-interaction 2>/dev/null || true

# ─── 7. Run migrations + seed ────────────────────────────────────────────────
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true

# ─── 8. Build frontend assets ────────────────────────────────────────────────
echo "Building frontend assets..."
npm run build

# ─── 9. Optimize ────────────────────────────────────────────────────────────
php artisan config:clear 2>/dev/null || true
php artisan route:clear  2>/dev/null || true
php artisan view:clear   2>/dev/null || true

# ─── 10. Start Laravel on port 3000 ─────────────────────────────────────────
echo "Starting WMS on port 3000..."
exec php artisan serve --host=0.0.0.0 --port=3000 --no-interaction
