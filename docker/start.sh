#!/usr/bin/env sh

set -eu

cd /var/www/html

mkdir -p \
    /var/data \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is required." >&2
    exit 1
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    sqlite_path="${DB_DATABASE:-/var/data/database.sqlite}"
    mkdir -p "$(dirname "$sqlite_path")"
    touch "$sqlite_path"
fi

php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"