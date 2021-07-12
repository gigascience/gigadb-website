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


