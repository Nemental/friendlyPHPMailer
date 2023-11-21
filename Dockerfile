FROM php:8.2-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

RUN composer require phpmailer/phpmailer "^6.8.1"

COPY --chown=www-data:www-data ./src /var/www/html
