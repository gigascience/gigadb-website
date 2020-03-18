FROM coppit/inotify-command

# upgrade from Alpine Linux 3.7 to 3.9 (as PDO api is too old in 3.7)
# Will be done in two stages to avoid the error
# ERROR: libcrypto1.1-1.1.1b-r1: trying to overwrite etc/ssl/openssl.cnf owned by libressl2.6-libcrypto-2.6.5-r0.

COPY repositories38 /etc/apk/repositories
RUN apk upgrade -U --available

COPY repositories39 /etc/apk/repositories
RUN apk upgrade -U --available

# needed for making command written in php that connect to postgresql
RUN	apk add --no-cache postgresql-dev php7 php7-pgsql php7-pdo php7-pdo_pgsql php7-json