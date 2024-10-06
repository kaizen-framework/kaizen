FROM php:8.3-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache \
	acl \
	file \
	gettext \
	git

WORKDIR /srv/app