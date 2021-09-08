FROM php:8.0-alpine

WORKDIR /var/www/html

# PHP dependencies
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# PCOV
RUN apk --update add gcc make g++ zlib-dev autoconf
RUN pecl install pcov && docker-php-ext-enable pcov

COPY ./api/composer.json ./

RUN composer install

COPY ./api ./

#USER root
ENTRYPOINT exec php -S 0.0.0.0:80 -t ./public
