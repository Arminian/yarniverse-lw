#!/bin/sh
set -e

echo "=== Starting Laravel Deployment ==="
cd /var/www/html

echo "=== Setting up environment ==="
if [ ! -f .env ] && [ -f .env.example ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Set database connection from DATABASE_URL
if [ -n "$DATABASE_URL" ]; then
    echo "Configuring database from DATABASE_URL..."
    # Remove existing DB_* lines and add fresh ones
    sed -i '/^DB_/d' .env 2>/dev/null || true
    echo "DB_CONNECTION=pgsql" >> .env
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
    php artisan key:generate --force -n
fi

echo "=== Running database migrations ==="
php artisan migrate --force -n || echo "Migrations warning"

echo "=== Running seeders ==="
php artisan db:seed --class=UserSeeder --force -n 2>/dev/null || echo "Seeding skipped"

echo "=== Setting up Filament Shield ==="
php artisan storage:link
php artisan vendor:publish --tag="filament-shield-config"
php artisan shield:setup -n --force 2>/dev/null || echo "Shield setup skipped"
php artisan shield:super-admin --panel=admin --user=3 -n 2>/dev/null || echo "Shield generate admin skipped"

echo "=== Caching Laravel configuration ==="
php artisan config:cache -n
php artisan route:cache -n
php artisan view:cache -n
php artisan event:cache -n 2>/dev/null || true

echo "=== Clearing permission cache ==="
php artisan permission:cache-reset 2>/dev/null || true

echo "=== Deployment Complete ==="