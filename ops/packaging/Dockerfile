ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm-buster


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
        wget \
        ca-certificates \
        --no-install-recommends && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


RUN docker-php-ext-install pdo_pgsql && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install pcntl && \
    docker-php-ext-install curl

# Install the PHP gd library
RUN docker-php-ext-install gd && \
    docker-php-ext-configure gd \
        --with-jpeg \
        --with-freetype && \
    docker-php-ext-install gd

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

# always run apt update when start and after add new source list, then clean up at end.
RUN apt-get update -yqq && \
    apt-get install -y apt-utils && \
    pecl channel-update pecl.php.net


ARG INSTALL_OPCACHE=false

RUN if [ ${INSTALL_OPCACHE} = true ]; then \
    docker-php-ext-install opcache \
;fi

# Copy opcache configration
#COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini
# not done here, host-mounted upon instantiation

ARG INSTALL_INTL=false

RUN if [ ${INSTALL_INTL} = true ]; then \
    # Install intl and requirements
    apt-get update -yq && \
    apt-get install -y zlib1g-dev libicu-dev g++ && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl \
;fi


ARG INSTALL_PGSQL=false

RUN if [ ${INSTALL_PGSQL} = true ]; then \
    # Install the pgsql extension
    docker-php-ext-install pgsql \
;fi

ARG INSTALL_PG_CLIENT=false

RUN if [ ${INSTALL_PG_CLIENT} = true ]; then \
    # Create folders if not exists (https://github.com/tianon/docker-brew-debian/issues/65)
    mkdir -p /usr/share/man/man1 && \
    mkdir -p /usr/share/man/man7 && \
    # Add the PostgreSQL Apt Repository
    echo "deb http://apt.postgresql.org/pub/repos/apt buster-pgdg main" > /etc/apt/sources.list.d/pgdg.list && \
    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add - && \
    # Install the pgsql client
    apt-get update -yq && \
    apt-get install -y postgresql-client-14 && \
    apt-get clean \
;fi


ARG INSTALL_XDEBUG=false

RUN if [ ${INSTALL_XDEBUG} = true ]; then \
  # Install the xdebug extension
  if [ $(php -r "echo PHP_MAJOR_VERSION;") = "5" ]; then \
    pecl install xdebug-2.5.5; \
  else \
    pecl install xdebug-2.9.8; \
  fi && \
  docker-php-ext-enable xdebug \
;fi


ARG INSTALL_NETCAT_JQ=false

RUN if [ ${INSTALL_NETCAT_JQ} = true ]; then \
    # Create folders if not exists (https://github.com/tianon/docker-brew-debian/issues/65)
    mkdir -p /usr/share/man/man1 && \
    mkdir -p /usr/share/man/man7 && \
    # Install netcat-openbsd and jq
    apt-get update -yq && \
    apt-get install -y netcat-openbsd inetutils-ping jq \
;fi

ARG INSTALL_GRAPHVIZ=false

RUN if [ ${INSTALL_GRAPHVIZ} = true ]; then \
    apt-get update -yq && \
    apt-get install -y graphviz \
;fi

ARG INSTALL_COMPOSER=false
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
RUN if [ ${INSTALL_COMPOSER} = true ]; then \
    apt-get update -yq \
    && apt-get install -y libzip-dev unzip wget \
    && docker-php-ext-install zip \
    && EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)" \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && ACTUAL_SIGNATURE="$(php -r "echo hash_file('SHA384', 'composer-setup.php');")" \
    && test $EXPECTED_SIGNATURE = $ACTUAL_SIGNATURE  \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet \
    && rm composer-setup.php \
    && composer config --global repo.packagist composer https://packagist.org \
;fi

ARG INSTALL_TIDEWAYS_XHPROF=false

RUN if [ ${INSTALL_TIDEWAYS_XHPROF} = true ]; then \
    curl -o tideways-xhprof.tar.gz -s -L https://github.com/tideways/php-xhprof-extension/archive/v4.1.6.tar.gz && \
    tar xzvf tideways-xhprof.tar.gz && \
    cd php-xhprof-extension* && \
    phpize && \
    ./configure && \
    make && \
    make install && \
    echo "extension=tideways.so" >  /usr/local/etc/php/conf.d/tideways_xhprof.ini && \
    pecl install mongodb && docker-php-ext-enable mongodb && \
    curl -o /usr/local/src/xhgui.tar.gz -s -L https://github.com/perftools/xhgui/archive/0.8.1.tar.gz && \
    cd /usr/local/src/ && \
    tar xzvf /usr/local/src/xhgui.tar.gz && \
    cd xhgui-0.8.1 && \
    apt-get install -y git && \
    composer install && \
    echo "auto_prepend_file=/usr/local/src/xhgui-0.8.1/external/header.php" > /usr/local/etc/php/conf.d/xhgui.ini \
;fi

ARG INSTALL_LIBSODIUM=false

RUN if [ ${INSTALL_LIBSODIUM} = true ]; then \
    apt-get update -yq && \
    apt-get install -y libsodium-dev && \
    pecl install libsodium && \
    echo "extension=sodium.so" >  /usr/local/etc/php/conf.d/libsodium.ini \
;fi

ARG INSTALL_GIT=false
RUN if [ ${INSTALL_GIT} = true ]; then \
    apt-get update -yq && \
    apt-get install -y git \
;fi

ARG INSTALL_APCU=false
ARG APCU_VERSION=5.1.21

RUN if [ ${INSTALL_APCU} = true ]; then \
    pecl install apcu-${APCU_VERSION} && \
    echo "extension=apcu.so" >  /usr/local/etc/php/conf.d/apcu.ini && \
    echo 'apc.shm_size="64M"' | tee -a /usr/local/etc/php/conf.d/apcu.ini \
;fi

ARG INSTALL_MAILPARSE=false
RUN if [ ${INSTALL_MAILPARSE} = true ]; then \
    # Install mailparse extension
    pecl install mailparse && \
    docker-php-ext-enable mailparse \
;fi

ARG INSTALL_CSSLINT_JSHINT=false
RUN if [ ${INSTALL_CSSLINT_JSHINT} = true ]; then \
    wget https://deb.nodesource.com/setup_12.x | bash - && \
    apt-get install -y nodejs npm && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*  && \
    npm install -g csslint jshint \
;fi

RUN usermod -u 1000 www-data

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
