#!/bin/bash
set -e

cd "$(dirname "$0")"

export GIT_SSH_COMMAND="ssh -i /var/www/.ssh/deploy_key -o StrictHostKeyChecking=no"

git -c safe.directory=/var/www/html pull origin main

composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# 修復 storage 權限（包含新建的 tenant 目錄）
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

php artisan queue:restart

echo "Deploy completed at $(date)"
