FROM php:8.0-alpine

# PHP dependencies
RUN docker-php-ext-install pdo pdo_mysql mysqli
# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . /var/www/html
WORKDIR /var/www/html

#USER root
ENTRYPOINT exec php -S 0.0.0.0:80 -t ./public
