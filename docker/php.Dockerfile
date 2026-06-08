FROM php:8.3-fpm-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/sessions.ini /usr/local/etc/php/conf.d/sessions.ini

RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

WORKDIR /var/www/app