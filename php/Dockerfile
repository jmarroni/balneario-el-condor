FROM php:5.6-cli

ARG USER_ID

RUN apk add --update --no-cache \
    zlib-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    bzip2-dev libzip-dev \
    pcre-dev ${PHPIZE_DEPS} \
    libbz2 bzip2-dev \
    tzdata \
    jpegoptim \
    pngquant \
    optipng \
    icu-dev \
    freetype-dev 

# Configure & Install Extension
RUN docker-php-ext-configure \
    opcache --enable-opcache &&\
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ &&\
    docker-php-ext-install \
    opcache \
    mysqli \
    pdo \
    pdo_mysql \
    sockets \
    json \
    intl \
    gd \
    xml \
    zip \
    bz2 \
    pcntl \
    bcmath && \
    pecl install redis  && \
    docker-php-ext-enable redis


COPY "php/php.ini" "$PHP_INI_DIR/php.ini"

# Composer install
RUN curl --silent --show-error https://getcomposer.org/installer | php && \
        mv composer.phar /usr/local/bin/composer

RUN adduser -D -s /bin/sh -u $USER_ID laravel -G www-data

ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.9/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=5ddf8ea26b56d4a7ff6faecdd8966610d5cb9d85

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

COPY cron /etc/crontabs/laravel

USER laravel

COPY entrypoint.sh /

ENTRYPOINT ["/entrypoint.sh"]

#ENTRYPOINT ["supercronic","/etc/crontabs/laravel"]

