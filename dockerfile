FROM serversideup/php:8.4-fpm-nginx-alpine
USER root

# Install Node.js
RUN apk add --no-cache nodejs npm

# Copy application
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
CMD /usr/local/bin/00-laravel-deploy.sh && /start.sh