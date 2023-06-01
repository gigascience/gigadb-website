FROM php:7.4-cli-buster

# Always run apt update when start and after add new source list, then clean up at end.
RUN apt-get update -yqq && \
    apt-get install -y apt-utils && \
    pecl channel-update pecl.php.net && \
    apt-get clean

# Basic dependencies
RUN apt-get update -yq && \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpq-dev \
        gnupg \
        wget \
        ca-certificates && \
        apt-get clean 

# Install extensions for the postgresql and PHP integration
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pgsql

ARG INSTALL_PG_CLIENT=true
RUN if [ ${INSTALL_PG_CLIENT} = true ]; then \
    # Create folders if not exists (https://github.com/tianon/docker-brew-debian/issues/65)
    mkdir -p /usr/share/man/man1 && \
    mkdir -p /usr/share/man/man7 && \
    # Add the PostgreSQL Apt Repository
    echo "deb http://apt.postgresql.org/pub/repos/apt buster-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - && \
    # Install the pgsql client
    apt-get update -yq && \
    apt-get install -y postgresql-client-14 \
;fi

# Install composer
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

# Bind mount Yii2 basic app
VOLUME ["/app"]
WORKDIR /app


