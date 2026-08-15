FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    freetype-dev \
    icu-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    zip

RUN docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    mbstring \
    opcache \
    zip

WORKDIR /app

CMD ["php-fpm"]
