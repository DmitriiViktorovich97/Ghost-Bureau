FROM php:8.4-fpm-alpine

RUN apk add --no-cache ca-certificates && update-ca-certificates

RUN apk add --no-cache \
        bash git unzip shadow curl \
        nodejs npm openssh-client \
        libcurl libpng libpq libzip libjpeg-turbo icu-libs \
        imagemagick redis ttf-dejavu \
        oniguruma-dev libxml2-dev sqlite-dev postgresql-libs

RUN apk add --no-cache --virtual .build-deps \
        autoconf g++ gcc libc-dev make linux-headers build-base \
        postgresql-dev libjpeg-turbo-dev libpng-dev icu-dev libzip-dev \
        libxml2-dev curl-dev pcre-dev pkgconf imagemagick-dev nasm ${PHPIZE_DEPS} \
 && docker-php-ext-configure gd --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
        bcmath pdo pdo_pgsql pdo_mysql pdo_sqlite gd intl pcntl zip \
        soap curl exif simplexml sockets \
 && pecl install redis imagick \
 && docker-php-ext-enable redis imagick \
 && apk del .build-deps \
 && rm -rf /tmp/pear /var/cache/apk/* /usr/share/man

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN sed -i 's/^pm = .*/pm = dynamic/' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/^pm.max_children = .*/pm.max_children = 10/' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/^pm.start_servers = .*/pm.start_servers = 2/' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 1/' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 3/' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html
EXPOSE 9000
CMD ["php-fpm"]
