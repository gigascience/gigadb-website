FROM php:8.2.3-cli-bullseye

# Allow app inside the container to detect whether they are running in container or not
ENV DOCKER_RUNNING=true

# Always run apt update when start and after add new source list, then clean up at end.
RUN apt-get update -yqq && \
    apt-get install -y apt-utils && \
    apt-get clean

# Basic dependencies
RUN apt-get update -yq && \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpq-dev && \
        apt-get clean

# Install Postgresql client
RUN docker-php-ext-install pdo_pgsql && \
    docker-php-ext-install pgsql && \
    apt-get install -y postgresql-client-13

# Install gum
RUN curl -L -o gum_0.14.5_amd64.deb 'https://github.com/charmbracelet/gum/releases/download/v0.14.5/gum_0.14.5_amd64.deb' && \
    dpkg --install gum_0.14.5_amd64.deb && \
    rm gum_0.14.5_amd64.deb

# Volume for Yii2 basic app because we need to write there (vendor and runtime)
VOLUME ["/gigadb/app/tools/files-metadata-console"]
WORKDIR /gigadb/app/tools/files-metadata-console
