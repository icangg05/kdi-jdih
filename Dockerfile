FROM dunglas/frankenphp:php8.3

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

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
