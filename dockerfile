# ------ stage 1: node build -------
FROM node:20-bullseye AS node-build
WORKDIR /app

# copy only package files for caching
COPY package*.json ./
RUN npm ci --legacy-peer-deps
COPY . .

# Vite production build
RUN npm run build

# ------ stage 2: php / composer build --------
FROM composer:2 AS composer-build
WORKDIR /app

# copy composer files and vendor install
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# copy app files (excluding node-built public assets)
COPY . .

# copy built assets from node-build into public
COPY --from=node-build /app/public/build /app/public/build

# ---------- stage 3: runtime (nginx + php-fpm) ----------
FROM richarvey/nginx-php-fpm:latest
USER root

# Install Node.js 20.x in the runtime container
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# copy app and vendor from composer-build
COPY --from=composer-build /app /var/www/html

# Copy nginx config
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy deploy script
COPY 00-laravel-deploy.sh /usr/local/bin/00-laravel-deploy.sh
RUN chmod +x /usr/local/bin/00-laravel-deploy.sh

# set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Run deployment script first, then start services
CMD /usr/local/bin/00-laravel-deploy.sh && /start.sh