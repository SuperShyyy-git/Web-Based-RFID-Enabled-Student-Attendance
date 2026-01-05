#!/bin/bash
set -ex

echo "=========================================="
echo "Starting application..."
echo "PORT env: ${PORT:-not set}"
echo "DB_HOST: ${DB_HOST:-not set}"
echo "=========================================="

echo "[1/5] Clearing config cache..."
php artisan config:clear
echo "[1/5] DONE"

echo "[2/5] Running migrations..."
php artisan migrate --force 2>&1 || {
    echo "Migration failed, but continuing..."
}
echo "[2/5] DONE"

echo "[3/5] Running seeders..."
php artisan db:seed --class=AdminSeeder --force 2>&1 || {
    echo "Seeder failed or already seeded, continuing..."
}
echo "[3/5] DONE"

echo "[4/5] Caching routes and views..."
php artisan route:cache 2>&1 || echo "Route cache failed, continuing..."
php artisan view:cache 2>&1 || echo "View cache failed, continuing..."
echo "[4/5] DONE"

echo "[5/5] Starting server on port ${PORT:-8080}..."
echo "=========================================="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
