FROM alpine:latest

USER root
RUN apk add --no-cache sudo

RUN apk --update --no-cache add swatch curl

WORKDIR /gigadb-cngb-backup-tool
COPY . .