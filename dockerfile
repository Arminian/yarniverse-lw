# ------ stage 1: php / composer build ------
FROM php:8.4-cli AS composer-build
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        intl \
        gd \
        mbstring \
        xml \
        bcmath \
        pcntl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy composer files and vendor install
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# copy app files
COPY . .

# ------ stage 2: runtime (nginx + php-fpm) ----------
FROM serversideup/php:8.4-fpm-nginx-alpine
USER root

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Copy application from composer-build stage
COPY --from=composer-build /app /var/www/html

# Copy custom nginx config
COPY nginx/default.conf /etc/nginx/conf.d/custom.conf

# Copy deploy script
COPY 00-laravel-deploy.sh /usr/local/bin/00-laravel-deploy.sh
RUN chmod +x /usr/local/bin/00-laravel-deploy.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

# Run deployment script first, then start services
CMD /usr/local/bin/00-laravel-deploy.sh && /start.sh