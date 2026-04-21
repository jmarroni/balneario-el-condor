#!/usr/bin/env bash
set -e

if [ -d "/var/www/html/storage" ]; then
    chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

exec "$@"
