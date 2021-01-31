FROM php:7.2-apache

# yaml extension
# RUN apt-get update -y \
#     && apt-get install -y libyaml-dev libyaml \
#     && pecl install yamL \

RUN apt-get update -y \
    && apt-get install -y libpng-dev libfreetype6-dev \
    && pecl install xdebug 
    
RUN docker-php-ext-enable xdebug \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/freetype2 \
    && docker-php-ext-install pdo mysqli pdo_mysql gd

RUN usermod -u 1000 www-data \
    && a2enmod rewrite
    #&& a2enmod yaml