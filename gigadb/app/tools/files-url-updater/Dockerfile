FROM php:7.4-cli-buster


# basic dependencies
RUN apt-get update -yq && \
    apt-get install -y --no-install-recommends  \
        procps \
        curl \
        ncftp \
        gnupg \
        libmemcached-dev \
        libz-dev \
        libzip-dev \
        zip \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        bc && \
        apt-get clean

# Install extensions for the postgresql and PHP integration
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pgsql

# Install the PHP bcmath extention
RUN docker-php-ext-install bcmath

## Install composer
RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && apt-get update -yq \
    && apt-get install -y unzip wget \
    && EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)" \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && ACTUAL_SIGNATURE="$(php -r "echo hash_file('SHA384', 'composer-setup.php');")" \
    && test $EXPECTED_SIGNATURE = $ACTUAL_SIGNATURE  \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet \
    && rm composer-setup.php \
    && composer config --global repo.packagist composer https://packagist.org

# always run apt update when start and after add new source list, then clean up at end.
RUN apt-get update -yqq && \
    apt-get install -y apt-utils && \
    pecl channel-update pecl.php.net && \
    apt-get clean

# Install legacy postgresql library
RUN echo "deb http://apt.postgresql.org/pub/repos/apt buster-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    curl -sL https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - && \
    apt-get update && \
    apt-get -y install postgresql-9.3 && \
    apt-get clean

# we will be bind mounting the Yii2 basic app
VOLUME ["/app"]
WORKDIR /app


