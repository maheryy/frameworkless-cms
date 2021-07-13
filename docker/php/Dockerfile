FROM php:7.4-fpm

WORKDIR /usr/share/nginx/html

RUN apt-get update \
    && apt-get install -y libpng-dev libfreetype6-dev libyaml-dev \
    && pecl install yamL \
    # xdebug
    && docker-php-ext-configure gd --with-freetype \
    && docker-php-ext-install pdo mysqli pdo_mysql gd \
    # && docker-php-ext-enable xdebug
    && docker-php-source delete
    
COPY php.ini /usr/local/etc/php/php.ini