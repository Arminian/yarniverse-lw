#!/usr/bin/env bash
set -e

echo "=== Starting Laravel Deployment ==="
echo "Current directory: $(pwd)"

cd /var/www/html

echo "=== Creating .env file if it doesn't exist ==="
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    fi
    php artisan key:generate --force
fi

echo "=== Installing Composer dependencies ==="
composer install --no-dev --optimize-autoloader --no-interaction

echo "=== Running database migrations ==="
php artisan migrate --force --no-interaction || echo "Migrations failed, continuing..."

echo "=== Running seeders ==="
php artisan db:seed --class=UserSeeder --force || echo "Seeding failed, continuing..."

echo "=== Caching Laravel configuration ==="
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== Setting up Filament Shield ==="
php artisan shield:install --minimal --force || true
php artisan shield:generate --all --force || true

echo "=== Clearing caches ==="
php artisan permission:cache-reset || true
php artisan optimize:clear || true

echo "=== Setting proper permissions ==="
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Deployment Complete ==="