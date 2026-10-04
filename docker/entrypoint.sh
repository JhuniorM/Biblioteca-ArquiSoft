#!/bin/sh
set -eu

export PORT="${PORT:-8080}"
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

php artisan config:cache

exec supervisord -c /etc/supervisord.conf