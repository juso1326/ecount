#!/bin/bash
set -e

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
CONTAINER="ecount_php"

# 偵測是否在 Docker 容器內執行
if [ -f /.dockerenv ]; then
    IN_DOCKER=true
else
    IN_DOCKER=false
fi

run_artisan() {
    if [ "$IN_DOCKER" = true ]; then
        php artisan "$@"
    else
        docker exec "$CONTAINER" php artisan "$@"
    fi
}

run_composer() {
    if [ "$IN_DOCKER" = true ]; then
        composer "$@"
    else
        docker exec "$CONTAINER" composer "$@"
    fi
}

echo "[1/5] Git pull..."
cd "$APP_DIR"
export GIT_SSH_COMMAND="ssh -i /var/www/.ssh/deploy_key -o StrictHostKeyChecking=no"
git -c safe.directory="$APP_DIR" pull origin main

echo "[2/5] Composer install..."
run_composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "[3/5] Migrate..."
run_artisan migrate --force

echo "[4/5] Cache..."
run_artisan config:cache
run_artisan route:cache
run_artisan view:cache

echo "[5/5] 修復 storage 權限..."
if [ "$IN_DOCKER" = true ]; then
    chown -R www-data:www-data /var/www/html/storage
    chmod -R 775 /var/www/html/storage
else
    docker exec "$CONTAINER" chown -R www-data:www-data /var/www/html/storage
    docker exec "$CONTAINER" chmod -R 775 /var/www/html/storage
fi

run_artisan queue:restart

echo "Deploy completed at $(date)"
