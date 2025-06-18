FROM php:8.2-apache

# 1. Install system dependencies (MySQL only)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev nodejs npm \
    # MySQL dependencies:
    mariadb-client libmariadb-dev \
    && docker-php-ext-install \
    # MySQL extensions:
    pdo_mysql mysqli \
    # Other required extensions:
    zip mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite && \
    sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    FallbackResource /index.php\n\
    </Directory>' >> /etc/apache2/apache2.conf

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy only composer files first (better layer caching)
COPY composer.json composer.lock ./

# 6. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 7. Copy the rest of the application
COPY . .

# 8. Run composer scripts
RUN composer run-script post-autoload-dump

# 9. Build frontend assets (if needed)
RUN if [ -f "package.json" ]; then \
    npm install && npm run build; \
    fi

# 10. Laravel setup (no migration here!)
RUN php artisan key:generate --force && \
    php artisan storage:link && \
    php artisan optimize

# 11. Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD ["apache2-foreground"]