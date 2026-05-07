#!/usr/bin/env bash
set -e  # Exit on any error

echo "=== Starting Laravel Deployment ==="
echo "Current directory: $(pwd)"
echo "Current user: $(whoami)"

# Ensure in the Laravel directory
cd /var/www/html

echo "=== Creating .env file if it doesn't exist ==="
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
    # Generate app key if not set
    if grep -q "APP_KEY=$\|APP_KEY=$" .env; then
        php artisan key:generate --force
    fi
fi

echo "=== Installing Composer dependencies ==="
composer install --no-dev --optimize-autoloader --no-interaction

echo "=== Running database migrations ==="
php artisan migrate --force --no-interaction

echo "=== Running seeders ==="
# Run specific seeder or all seeders
php artisan db:seed --class=UserSeeder --force || true
php artisan db:seed --force || true

echo "=== Installing NPM dependencies ==="
npm ci --legacy-peer-deps || npm install --legacy-peer-deps

echo "=== Building frontend assets ==="
npm run build

echo "=== Caching Laravel configuration ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "=== Setting up Filament Shield ==="
# Generate Shield permissions if they don't exist
php artisan shield:install --minimal --force || true
php artisan shield:generate --all --force || true

# Reset permission cache
php artisan permission:cache-reset

echo "=== Setting proper permissions ==="
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Deployment Complete ==="