FROM golang:alpine3.9
RUN apk --no-cache add git
RUN go get github.com/jsha/minica
RUN mkdir /ca
WORKDIR /ca
ENTRYPOINT ["minica"]