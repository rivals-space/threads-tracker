#syntax=docker/dockerfile:1.4
# Use the base image provided by your internal repository for both production and development builds
ARG PRIVATE_REGISTRY

FROM $PRIVATE_REGISTRY/internal/symfony-base:latest AS base
# Production build
FROM base as app_php_prod

USER root
# For sentry
RUN set -eux; \
    install-php-extensions excimer

COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/
COPY --link --chown=root:root docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

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
FROM base as app_php_dev

USER root
RUN set -eux; \
    install-php-extensions xdebug

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

USER web
ENV APP_ENV=dev \
    APP_VERSION=dev
