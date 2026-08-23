#!/usr/bin/env sh
set -eu

is_valid_app_key() {
    php -r '
        $key = getenv("APP_KEY") ?: "";
        if (!str_starts_with($key, "base64:")) {
            exit(1);
        }
        $decoded = base64_decode(substr($key, 7), true);
        if ($decoded === false || strlen($decoded) !== 32) {
            exit(1);
        }
        exit(0);
    '
}

write_env_key() {
    key="$1"
    if [ -f .env ] && grep -q '^APP_KEY=' .env; then
        sed -i "s|^APP_KEY=.*$|APP_KEY=${key}|" .env
    else
        printf '\nAPP_KEY=%s\n' "$key" >> .env
    fi
}

mkdir -p /var/data
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch /var/data/database.sqlite

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ -n "${APP_KEY:-}" ]; then
    APP_KEY=$(printf '%s' "$APP_KEY" | sed "s/^'//;s/'$//;s/^\"//;s/\"$//")
    export APP_KEY
fi

if [ -z "${APP_KEY:-}" ] || ! is_valid_app_key; then
    APP_KEY=$(php artisan key:generate --show --no-interaction)
    export APP_KEY
    write_env_key "$APP_KEY"
fi

php artisan config:clear
php artisan route:clear
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan migrate --force

exec php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"