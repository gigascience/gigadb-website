FROM php:8.0-fpm-bullseye

RUN usermod -u 1000 www-data

# basic dependencies
RUN apt-get update -yq && \
    apt-get install -y --no-install-recommends \
        procps \
        curl \
        libcurl4-gnutls-dev \
        libmemcached-dev \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        libmcrypt-dev \
        bc \
        gnupg \
        --no-install-recommends && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo_pgsql

WORKDIR /app

CMD ["php-fpm"]