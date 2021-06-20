FROM php:7.4-apache

RUN apt-get update -y \
    && apt-get install -y libpng-dev libfreetype6-dev libyaml-dev \
    && pecl install yamL
#    xdebug

    
RUN docker-php-ext-configure gd --with-freetype \
    && docker-php-ext-install pdo mysqli pdo_mysql gd
#    docker-php-ext-enable xdebug

RUN usermod -u 1000 www-data \
    && a2enmod rewrite \