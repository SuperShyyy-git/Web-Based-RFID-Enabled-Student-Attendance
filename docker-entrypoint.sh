#!/bin/bash

echo "=========================================="
echo "Starting application..."
echo "PORT env: ${PORT:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "APP_KEY: ${APP_KEY:-not set}"
echo "=========================================="

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "[0/4] Generating APP_KEY..."
    php artisan key:generate --force 2>&1 || echo "Key generation failed, continuing..."
fi

echo "[1/4] Clearing config cache..."
php artisan config:clear 2>&1 || echo "Config clear failed, continuing..."

echo "[2/4] Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration failed, continuing..."

echo "[3/4] Running seeders..."
php artisan db:seed --class=AdminSeeder --force 2>&1 || echo "Seeder failed or already seeded, continuing..."

echo "[4/4] Starting server on port ${PORT:-8080}..."
echo "=========================================="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
