#!/bin/sh
set -e

# Generate .env from .env.example if .env doesn't exist
if [ ! -f /var/www/html/.env ]; then
  cp /var/www/html/.env.example /var/www/html/.env
fi

# Override .env values with Render environment variables (if set)
[ -n "$DB_HOST" ] && sed -i "s|^DB_HOST=.*|DB_HOST=$DB_HOST|" /var/www/html/.env
[ -n "$DB_PORT" ] && sed -i "s|^DB_PORT=.*|DB_PORT=$DB_PORT|" /var/www/html/.env
[ -n "$DB_DATABASE" ] && sed -i "s|^DB_DATABASE=.*|DB_DATABASE=$DB_DATABASE|" /var/www/html/.env
[ -n "$DB_USERNAME" ] && sed -i "s|^DB_USERNAME=.*|DB_USERNAME=$DB_USERNAME|" /var/www/html/.env
[ -n "$DB_PASSWORD" ] && sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" /var/www/html/.env
[ -n "$APP_KEY" ] && sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" /var/www/html/.env
[ -n "$APP_URL" ] && sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" /var/www/html/.env
[ -n "$APP_ENV" ] && sed -i "s|^APP_ENV=.*|APP_ENV=$APP_ENV|" /var/www/html/.env
[ -n "$APP_DEBUG" ] && sed -i "s|^APP_DEBUG=.*|APP_DEBUG=$APP_DEBUG|" /var/www/html/.env
[ -n "$SECRET_KEY" ] && sed -i "s|^SECRET_KEY=.*|SECRET_KEY='$SECRET_KEY'|" /var/www/html/.env
[ -n "$VIKEY" ] && sed -i "s|^VIKEY=.*|VIKEY='$VIKEY'|" /var/www/html/.env

# Configure PHP error reporting so deprecations don't corrupt JSON API responses
echo "display_errors = Off" > /usr/local/etc/php/conf.d/error-logging.ini
echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/conf.d/error-logging.ini

# Clear configuration cache so environment variables are loaded cleanly
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Execute Apache in foreground
exec apache2-foreground