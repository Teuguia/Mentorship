#!/usr/bin/env sh
set -eu

exec php artisan reverb:start --host=0.0.0.0 --port="${REVERB_SERVER_PORT:-8080}"
