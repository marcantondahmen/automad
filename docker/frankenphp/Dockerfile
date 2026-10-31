FROM dunglas/frankenphp:1.12.6-php8.5-alpine

RUN install-php-extensions gd zip imagick

COPY Caddyfile /etc/frankenphp/Caddyfile

WORKDIR /app
EXPOSE 80
