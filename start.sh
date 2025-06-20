#!/bin/bash
echo "Environment variables:"
cat .env || echo "No .env file found"
echo "Testing MySQL connection..."
mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD -e "SELECT 1" || { echo "MySQL connection failed"; exit 1; }
chmod -R 775 storage
php artisan config:clear
php artisan cache:clear
php artisan key:generate --force
php artisan storage:link
php artisan optimize
php artisan migrate --force
apache2-foreground