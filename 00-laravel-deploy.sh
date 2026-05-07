#!/usr/bin/env bash
set -e

echo "=== Starting Laravel Deployment ==="
cd /var/www/html

echo "=== Creating .env file if it doesn't exist ==="
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    fi
fi

# Generate app key if not set
if grep -q "APP_KEY=$\|APP_KEY=$" .env 2>/dev/null || ! grep -q "APP_KEY=" .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "=== Installing NPM dependencies ==="
npm ci --legacy-peer-deps 2>/dev/null || npm install --legacy-peer-deps

echo "=== Building frontend assets ==="
npm run build

echo "=== Running database migrations ==="
php artisan migrate --force --no-interaction || echo "Migrations warning (may be first deploy)"

echo "=== Running seeders ==="
php artisan db:seed --class=UserSeeder --force 2>/dev/null || echo "Seeding skipped or failed"

echo "=== Setting up Filament Shield ==="
php artisan shield:install --minimal --force 2>/dev/null || echo "Shield install skipped"
php artisan shield:generate --all --force 2>/dev/null || echo "Shield generate skipped"

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