#!/bin/bash

echo "✅ Starting deployment..."

# 1. Show environment load status
echo "Loading environment variables from Render dashboard..."
# Debug: List relevant environment variables
env | grep -E 'APP_|DB_|SESSION_|CACHE_' || echo "No relevant environment variables found"
# Debug: List all environment variables if none found
if ! env | grep -E 'APP_|DB_|SESSION_|CACHE_' > /dev/null; then
  echo "All environment variables for debugging:"
  env
fi

# 2. Check required database variables
echo "Checking required database variables..."
if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
  echo "❌ Missing one or more required DB environment variables (DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD)"
  exit 1
fi
echo "✅ Database variables present."

# 3. Ensure storage/logs directory exists and has correct permissions
echo "Setting up storage and logs permissions..."
mkdir -p /var/www/html/storage/logs
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# Ensure laravel.log is writable
touch /var/www/html/storage/logs/laravel.log
chmod 664 /var/www/html/storage/logs/laravel.log
chown www-data:www-data /var/www/html/storage/logs/laravel.log
echo "✅ Permissions set for storage and logs."

# 4. Test MySQL connection
echo "Testing MySQL connection..."
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" || {
  echo "❌ MySQL connection failed"
  exit 1
}
echo "✅ MySQL connection successful."

# 5. Laravel setup
echo "Running Laravel setup commands..."
php artisan config:clear
php artisan cache:clear
php artisan storage:link
php artisan optimize
php artisan migrate --force

# 6. Start Apache
echo "🚀 Launching Apache..."
exec apache2-foreground