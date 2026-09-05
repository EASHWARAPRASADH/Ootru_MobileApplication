#!/bin/sh
set -e

# Clear configuration cache so environment variables from Render are loaded cleanly
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Execute Apache in foreground
exec apache2-foreground