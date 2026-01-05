#!/bin/bash
set -e

echo "Starting application..."
php artisan config:clear

echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --class=AdminSeeder --force || true

echo "Caching routes and views..."
php artisan route:cache
php artisan view:cache

echo "Starting server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
