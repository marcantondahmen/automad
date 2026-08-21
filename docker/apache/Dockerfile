FROM php:8.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
	libmagickwand-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install gd zip \
	&& pecl install imagick-3.8.1 && docker-php-ext-enable imagick \
	&& apt-get purge -y --auto-remove $PHPIZE_DEPS libmagickwand-dev \
	&& rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
