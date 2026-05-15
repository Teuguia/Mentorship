#!/usr/bin/env sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-interaction || true
fi

php artisan optimize:clear || true
php artisan storage:link || true
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
