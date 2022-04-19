FROM openjdk:8

RUN apt-get update -yq && \
    apt-get install -y libarchive-tools  # Provides bsdtar

VOLUME ["/tool"]
WORKDIR /tool
