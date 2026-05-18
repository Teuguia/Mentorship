#!/usr/bin/env sh
set -eu

log() {
    printf '[start-web] %s\n' "$1"
}

run_with_retry() {
    label="$1"
    shift
    attempts="${STARTUP_RETRY_ATTEMPTS:-5}"
    delay="${STARTUP_RETRY_DELAY:-5}"
    count=1

    until "$@"; do
        if [ "$count" -ge "$attempts" ]; then
            log "$label failed after $count attempt(s)"
            return 1
        fi

        log "$label failed on attempt $count/$attempts; retrying in ${delay}s"
        count=$((count + 1))
        sleep "$delay"
    done
}

log "booting Laravel web service"
log "php $(php -r 'echo PHP_VERSION;')"
log "APP_ENV=${APP_ENV:-unset} APP_DEBUG=${APP_DEBUG:-unset} APP_URL=${APP_URL:-unset}"
log "DB_CONNECTION=${DB_CONNECTION:-unset} DB_HOST_SET=$([ -n "${DB_HOST:-}" ] && echo yes || echo no) DB_DATABASE_SET=$([ -n "${DB_DATABASE:-}" ] && echo yes || echo no)"
log "BROADCAST_CONNECTION=${BROADCAST_CONNECTION:-unset} REVERB_HOST_SET=$([ -n "${REVERB_HOST:-}" ] && echo yes || echo no)"

if [ -z "${APP_KEY:-}" ]; then
    log "APP_KEY is missing; generating an ephemeral key"
    php artisan key:generate --force --no-interaction || true
fi

log "clearing Laravel caches"
php artisan optimize:clear || true

log "linking storage"
php artisan storage:link || true

log "running migrations"
run_with_retry "migrations" php artisan migrate --force --no-interaction

log "ensuring admin account when configured"
php artisan app:ensure-admin --no-interaction || true

log "caching config, routes and views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

log "starting server on 0.0.0.0:${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
