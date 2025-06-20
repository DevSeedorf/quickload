#!/bin/bash

echo "✅ Starting deployment..."

# 1. Show environment load status
echo "Loading environment variables from Render dashboard..."
# Debug: List relevant environment variables
env | grep -E 'APP_|DB_|SESSION_|CACHE_' || echo "No relevant environment variables found"

# 2. Create Laravel .env file if not present
if [ ! -f .env ]; then
  echo "Generating .env file..."
  # Check if required variables are set
  if [ -z "$APP_KEY" ] || [ -z "$DB_HOST" ] || [ -z "$DB_USERNAME" ]; then
    echo "❌ Missing critical environment variables (APP_KEY, DB_HOST, DB_USERNAME)"
    exit 1
  fi
  cat <<EOF > .env
APP_KEY=$APP_KEY
APP_ENV=$APP_ENV
APP_DEBUG=$APP_DEBUG
APP_URL=$APP_URL

DB_CONNECTION=$DB_CONNECTION
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

CACHE_DRIVER=$CACHE_DRIVER
SESSION_DRIVER=$SESSION_DRIVER
EOF
  chmod 644 .env
  echo "✅ .env file created."
else
  echo ".env file already exists, skipping creation."
fi

# 3. Test MySQL connection
echo "Testing MySQL connection..."
if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_PASSWORD" ]; then
  echo "❌ Missing one or more required DB environment variables."
  exit 1
fi

mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" || {
  echo "❌ MySQL connection failed"
  exit 1
}
echo "✅ MySQL connection successful."

# 4. Laravel setup
echo "Running Laravel setup commands..."
chmod -R 775 storage bootstrap/cache
php artisan config:clear
php artisan cache:clear
php artisan key:generate --force
php artisan storage:link
php artisan optimize
php artisan migrate --force

# 5. Start Apache
echo "🚀 Launching Apache..."
exec apache2-foreground