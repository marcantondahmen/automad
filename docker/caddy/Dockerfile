FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
	freetype-dev \
    libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    zlib-dev \
    zip

RUN docker-php-ext-configure gd \
	&& docker-php-ext-install -j$(nproc) gd zip

WORKDIR /app

CMD ["php-fpm"]
