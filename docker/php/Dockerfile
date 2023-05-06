FROM php:8.2-fpm

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        git \
        curl && \
    docker-php-ext-install \
        pdo_mysql \
        zip \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache \
        soap && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && \
    rm -rf /var/lib
