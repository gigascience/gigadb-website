FROM alpine:latest

USER root
RUN apk add --no-cache sudo

RUN apk --update --no-cache add swatch curl unzip \
    && curl -O https://downloads.rclone.org/rclone-current-linux-amd64.zip \
    && unzip rclone-current-linux-amd64.zip \
    && cd rclone-*-linux-amd64 \
    && sudo cp rclone /usr/bin/ \
    && sudo chown root:root /usr/bin/rclone \
    && sudo chmod 755 /usr/bin/rclone

WORKDIR /gigadb-cngb-backup-tool
COPY . .