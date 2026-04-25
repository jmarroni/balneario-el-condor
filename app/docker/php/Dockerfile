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

# ============== NODE BUILDER ==============
FROM node:20-alpine AS node-builder

WORKDIR /build
COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund

COPY resources/ resources/
COPY vite.config.* tailwind.config.* postcss.config.* ./
RUN npm run build

# ============== PROD ==============
FROM base AS prod

# OPcache + tuning prod
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/zz-prod.ini

# Código fuente (sin .git ni node_modules ni storage — controlado por .dockerignore)
COPY --chown=www-data:www-data . /var/www/html

# Composer install no-dev con autoload optimizado
# --no-scripts en ambos: post-autoload-dump dispara package:discover que carga config/scribe.php
# (Scribe es dev-only). En runtime, php artisan package:discover se ejecuta con cache.
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
 && composer dump-autoload --classmap-authoritative --no-scripts

# Assets compilados desde el node-builder
COPY --from=node-builder --chown=www-data:www-data /build/public/build /var/www/html/public/build

# Caches de Artisan (route + view solamente al build).
# config:cache y event:cache se hacen en el entrypoint en runtime, cuando .env está
# disponible. Hacerlo en build evalúa configs que pueden referenciar paquetes dev-only
# (ej: config/scribe.php usa Knuckles\Scribe\Config\AuthIn).
RUN php artisan route:cache \
 && php artisan view:cache

# Permisos finales
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && find /var/www/html/storage -type d -exec chmod 775 {} \; \
 && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;

USER www-data

# Healthcheck simple — verifica que PHP CLI responde dentro del container
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD php -r "exit(0);" || exit 1

CMD ["php-fpm"]
