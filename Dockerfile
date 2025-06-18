FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev nodejs npm \
    && docker-php-ext-install zip pdo mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy files in optimal order for caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress
COPY . .

# Build assets if needed
RUN if [ -f "package.json" ]; then npm install && npm run build; fi

# Laravel setup
RUN php artisan key:generate --force && \
    php artisan optimize && \
    php artisan storage:link

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Configure Apache
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 8080
CMD ["apache2-foreground"]