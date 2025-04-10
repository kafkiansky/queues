FROM golang:1.24-alpine

RUN apk update && \
    apk upgrade && \
    apk add bash git && \
    rm -rf /var/cache/apk*

WORKDIR /app

ENV GO111MODULE=on \
    CGO_ENABLED=0 \
    GOOS=linux \
    GOARCH=amd64
