FROM php:8.5-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache \
	acl \
	file \
	gettext \
	git \
    curl

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions xdebug yaml

USER 1000:1000

WORKDIR /app

COPY --chmod=0555 docker/php/entrypoint.sh /entrypoint

ENTRYPOINT ["/entrypoint"]
