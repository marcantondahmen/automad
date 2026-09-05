FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
	$PHPIZE_DEPS \
	imagemagick-dev \
	freetype-dev \
    libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    zlib-dev \
    zip \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
	&& docker-php-ext-install gd zip \
	&& pecl install imagick && docker-php-ext-enable imagick \
	&& apk del $PHPIZE_DEPS

WORKDIR /app

CMD ["php-fpm"]
