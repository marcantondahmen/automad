FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    zip

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

RUN docker-php-ext-install \
    gd \
    intl \
    opcache \
    zip

RUN a2enmod rewrite
