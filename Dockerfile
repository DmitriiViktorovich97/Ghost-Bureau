FROM php:8.4-cli-alpine

RUN apk add --no-cache \
        bash git unzip curl \
        oniguruma-dev libxml2-dev sqlite-dev \
        libpng-dev libjpeg-turbo-dev icu-dev libzip-dev

RUN docker-php-ext-install \
        pdo pdo_sqlite mbstring exif pcntl bcmath gd intl zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache database \
 && chmod -R 775 storage bootstrap/cache database

EXPOSE 3000

CMD php artisan migrate --force --seed \
 && php artisan config:cache \
 && php artisan serve --host=0.0.0.0 --port=3000
