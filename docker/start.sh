#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo "Set APP_KEY to the output of php artisan key:generate --show." >&2
    exit 1
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    database_path="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$database_path")"
    touch "$database_path"
    chown -R www-data:www-data "$(dirname "$database_path")"
fi

public_storage_path="${PUBLIC_STORAGE_PATH:-/var/www/html/storage/app/public}"
mkdir -p "$public_storage_path"
chown -R www-data:www-data "$public_storage_path" storage bootstrap/cache

php artisan migrate --force

# Seed once so restarting does not reset edited sample accounts.
seed_marker="$(dirname "${DB_DATABASE:-/var/www/html/database/database.sqlite}")/.pos-seeded"
if [ ! -f "$seed_marker" ]; then
    php artisan db:seed --force
    touch "$seed_marker"
fi

php artisan storage:link --force
php artisan config:cache
php artisan view:cache
exec apache2-foreground
