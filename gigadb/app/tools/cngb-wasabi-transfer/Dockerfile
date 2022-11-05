FROM alpine:3.16

ARG RCLONE_VERSION=v1.60.0
ARG ARCH=amd64

RUN apk update \
    && apk add \
        openssl \
        ca-certificates \
        fuse \
        curl \
    && cd /tmp \
    && curl "https://downloads.rclone.org/${RCLONE_VERSION}/rclone-${RCLONE_VERSION}-linux-${ARCH}.zip" -o "/tmp/rclone-${RCLONE_VERSION}-linux-${ARCH}.zip" \
    && unzip /tmp/rclone-*-linux-${ARCH}.zip \
    && mv /tmp/rclone-*-linux-${ARCH}/rclone /usr/bin \
    && rm -r /tmp/rclone* 

CMD ["rclone", "version"]
