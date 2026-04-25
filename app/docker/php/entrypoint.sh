#!/bin/sh
set -e

if [ "${APP_ENV}" = "production" ]; then
    # En prod: solo ejecutar migrations si MIGRATE_ON_BOOT=true (off por default)
    if [ "${MIGRATE_ON_BOOT:-false}" = "true" ]; then
        php artisan migrate --force --no-interaction
    fi
    php artisan config:cache > /dev/null
    php artisan route:cache > /dev/null
    php artisan view:cache > /dev/null
    php artisan event:cache > /dev/null || true
else
    # Dev: comportamiento existente (no cache, posible composer install si vendor falta)
    if [ -d "/var/www/html/storage" ]; then
        chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    fi
    if [ ! -d /var/www/html/vendor ]; then
        composer install --no-interaction
    fi
fi

exec "$@"
