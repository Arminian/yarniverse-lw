# ------ stage 1: php / composer build ------
FROM php:8.4-cli AS composer-build
WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip pdo pdo_pgsql pdo_mysql intl gd mbstring xml bcmath pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ------ stage 2: runtime ------
FROM serversideup/php:8.4-fpm-nginx-alpine
USER root

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Copy application
COPY --from=composer-build /app /var/www/html

# Copy nginx config
COPY nginx/default.conf /etc/nginx/conf.d/custom.conf

# Copy entrypoint scripts with proper permissions
COPY --chmod=755 00-laravel-deploy.sh /etc/entrypoint.d/99-laravel-deploy.sh

# Register scripts with S6 Overlay
RUN docker-php-serversideup-s6-init

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data