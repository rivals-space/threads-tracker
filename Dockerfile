#syntax=docker/dockerfile:1.4
FROM dunglas/frankenphp:latest-php8.3-alpine AS frankenphp_upstream

FROM frankenphp_upstream AS frankenphp_base

WORKDIR /srv/app

RUN apk add --no-cache \
		acl \
        curl \
		file \
		gettext \
		git \
	;

RUN set -eux; \
    install-php-extensions \
        @composer \
    	amqp \
    	apcu \
    	intl \
		igbinary \
		opcache \
    	openssl \
    	pdo_pgsql \
		redis \
    	zip \
    ; \
    printf '\nsession.serialize_handler=igbinary\napc.serializer=igbinary' >> "$PHP_INI_DIR/conf.d/docker-php-ext-igbinary.ini"

# Add config
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link --chown=root:root docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

ARG UID=1000
ARG GID=1000

# Create a dedicated user and group
RUN set -eux; \
	addgroup -g $GID web; \
    adduser -u $UID -D -G web web; \
    mkdir -p /home/web; chown $UID:$GID /home/web; \
    mkdir -p /srv/app; chown $UID:$GID /srv/app; \
    mkdir -p /var/run/php; chown $UID:$GID /var/run/php

USER web
WORKDIR /srv/app

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

FROM frankenphp_base as app_php_prod

USER root
# For sentry
RUN set -eux; \
    install-php-extensions excimer

COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

USER web

COPY --link --chown=1000:1000 composer.* symfony.* ./
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
    composer clear-cache

COPY --chown=1000:1000 --link . .
RUN set -eux; \
    rm -Rf docker/; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer dump-env prod; \
    composer run-script --no-dev post-install-cmd; \
    chmod +x bin/console; sync

ARG APP_VERSION
ENV APP_ENV=prod
ENV APP_VERSION=$APP_VERSION

# Development build
FROM frankenphp_base as app_php_dev

USER root
RUN set -eux; \
    install-php-extensions xdebug

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

USER web
ENV APP_ENV=dev \
    APP_VERSION=dev
