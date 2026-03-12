#!/bin/bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed database if requested
if [ "$APP_SEED" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Cache configuration, routes, and views for production
echo "Caching configuration..."
php artisan config:cache
echo "Caching routes..."
php artisan route:cache
echo "Caching views..."
php artisan view:cache

# Execute the original CMD (start Apache)
echo "Starting Apache..."
exec apache2-foreground
