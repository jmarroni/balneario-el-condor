# syntax=docker/dockerfile:1.7

# ============== BASE ==============
FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
      git curl bash \
      zip unzip \
      icu-dev libzip-dev oniguruma-dev \
      libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
      $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
      pdo_mysql mbstring zip intl bcmath gd exif pcntl

RUN pecl install redis \
 && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ============== DEV ==============
FROM base AS dev

RUN apk add --no-cache nodejs npm linux-headers

RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

ARG HOST_UID=1000
ARG HOST_GID=1000
RUN deluser www-data \
 && addgroup -g ${HOST_GID} -S www-data \
 && adduser -u ${HOST_UID} -S -G www-data www-data

USER www-data
CMD ["php-fpm"]

# ============== PROD ==============
FROM base AS prod

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data
CMD ["php-fpm"]
