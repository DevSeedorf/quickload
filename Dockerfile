FROM php:8.2-apache

# Install PHP extensions & dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy only composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies with more verbose output
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy Laravel files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets (if using Vite/Mix)
RUN npm install && npm run build

# Generate Laravel key & optimize
RUN php artisan key:generate --force
RUN php artisan optimize

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Enable Apache rewrite
RUN a2enmod rewrite

# Expose port 8080 (Apache runs here, Render maps to 10000)
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]