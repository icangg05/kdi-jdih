FROM dunglas/frankenphp:php8.5.12

ENV SERVER_NAME=:8000
ENV APP_ENV=production
ENV APP_DEBUG=true

RUN apt-get update && apt-get install -y nodejs npm

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache

COPY php/php.ini /usr/local/etc/php/php.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
