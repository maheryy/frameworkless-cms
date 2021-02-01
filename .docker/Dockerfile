FROM php:7.2-apache

RUN apt-get update -y \
    && apt-get install -y libpng-dev libfreetype6-dev libyaml-dev \
    && pecl install xdebug yamL
    
RUN docker-php-ext-enable xdebug \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/freetype2 \
    && docker-php-ext-install pdo mysqli pdo_mysql gd

RUN usermod -u 1000 www-data \
    && a2enmod rewrite
    #&& a2enmod yaml