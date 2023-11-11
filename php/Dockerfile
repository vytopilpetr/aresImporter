FROM php:8.2-fpm-alpine

RUN apk --no-cache add zlib-dev libzip-dev zip bash

RUN docker-php-ext-install pdo pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --2

WORKDIR /var/www

