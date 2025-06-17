#!/usr/bin/env bash
# render-build.sh

# Install PHP dependencies
composer install --no-interaction --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate --force

# Optimize Laravel
php artisan optimize

# Install node dependencies and build assets (if needed)
npm install && npm run prod