#!/bin/sh
supercronic /etc/crontabs/laravel &
docker-php-entrypoint php-fpm
