#!/usr/bin/env sh
set -eu

php artisan key:generate --force --no-interaction || true
php artisan storage:link || true
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
