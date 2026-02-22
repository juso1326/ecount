#!/bin/bash
set -e

cd "$(dirname "$0")"

git config --global --add safe.directory /var/www/html

git pull origin main

composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:restart

echo "Deploy completed at $(date)"
