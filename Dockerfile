FROM php:8.2-apache

# 1. Install system dependencies with zip extension
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev nodejs npm \
    && docker-php-ext-install zip pdo mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/conf-available/*.conf

# 3. Set working directory
WORKDIR /var/www/html

# 4. Copy only composer files first (for caching)
COPY composer.json composer.lock ./

# 5. Install PHP dependencies (ONCE)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts

# 6. Copy the rest of the application
COPY . .

# 7. Run composer scripts separately
RUN composer run-script post-autoload-dump

# 8. Build frontend assets (if needed)
RUN if [ -f "package.json" ]; then \
    npm install && npm run build; \
    fi

# 9. Generate Laravel key & optimize
RUN php artisan key:generate --force && \
    php artisan optimize && \
    php artisan storage:link

# 10. Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# 11. Enable Apache rewrite
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf


EXPOSE 8080
CMD ["apache2-foreground"]