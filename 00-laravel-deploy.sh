#!/bin/sh
set -e

echo "=== Starting Laravel Deployment ==="
cd /var/www/html

echo "=== Setting up environment ==="
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Ensure APP_KEY exists
if [ -n "$APP_KEY" ]; then
    if ! grep -q "APP_KEY=[A-Za-z0-9]\+\S*" .env 2>/dev/null; then
        echo "APP_KEY=$APP_KEY" >> .env
    fi
fi

# Generate app key if still empty
if ! grep -q "APP_KEY=[A-Za-z0-9]\+\S*" .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "Configuring database from DATABASE_URL..."
echo "DB_CONNECTION=pgsql" >> .env

echo "=== Installing NPM dependencies ==="
npm ci --legacy-peer-deps 2>/dev/null || npm install --legacy-peer-deps

echo "=== Building frontend assets ==="
npm run build

echo "=== Clearing caches ==="
php artisan optimize:clear 2>/dev/null || true

echo "=== Running database migrations ==="
php artisan migrate --force --no-interaction || echo "Migrations warning"

echo "=== Running seeders ==="
php artisan db:seed --class=UserSeeder --force 2>/dev/null || echo "Seeding skipped"

echo "=== Setting up Filament Shield ==="
php artisan shield:install --fresh 2>/dev/null || echo "Shield install skipped"
php artisan shield:generate --all 2>/dev/null || echo "Shield generate skipped"

echo "=== Caching Laravel configuration ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

echo "=== Clearing permission cache ==="
php artisan permission:cache-reset 2>/dev/null || true

echo "=== Setting proper permissions ==="
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Deployment Complete ==="