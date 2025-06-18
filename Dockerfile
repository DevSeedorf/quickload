FROM php:8.2-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-install zip pdo mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite

# Change DocumentRoot to /public and disable directory listing
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    echo '<Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>' >> /etc/apache2/apache2.conf

# 4. Set working directory
WORKDIR /var/www/html

# 5. Copy application files
COPY . .

# 6. Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Build frontend assets (if needed)
RUN if [ -f "package.json" ]; then \
    npm install && npm run build; \
    fi

# 8. Laravel setup
RUN php artisan key:generate --force && \
    php artisan optimize && \
    php artisan storage:link

# 9. Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080
CMD ["apache2-foreground"]
