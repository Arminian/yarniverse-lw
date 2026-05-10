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

echo "=== Setting permissions ==="
# Ensure storage directory exists with proper structure
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/logs

# Set ownership and permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Running database migrations ==="
php artisan migrate --force -n || echo "Migrations warning"

echo "=== Setting up Shield ==="
php artisan shield:install admin -n 2>/dev/null || echo "Shield setup skipped"

echo "=== Running seeders ==="
php artisan db:seed --class=UserSeeder --force -n 2>/dev/null || echo "Seeding skipped"

echo "=== Generating Shield permissions for all resources ==="
php artisan shield:generate --all --panel=admin --option=policies_and_permissions || echo "Shield permissions skipped"

echo "=== Setting up Shield ==="
php artisan shield:super-admin --panel=admin --user=1 -n 2>/dev/null || echo "Shield generate admin skipped"

echo "=== Caching Laravel configuration ==="
php artisan config:cache -n
php artisan route:cache -n
php artisan view:cache -n
php artisan event:cache -n 2>/dev/null || true

echo "=== Deployment Complete ==="