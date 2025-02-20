#syntax=docker/dockerfile:1.4

# Versions
FROM dunglas/frankenphp:1.4.0-php8.4.2-bookworm AS frankenphp_upstream

ARG SERVER_NAME=":80"
ENV SERVER_NAME=${SERVER_NAME}
ARG BUILD_TIME
ENV BUILD_TIME=$BUILD_TIME
ENV WORKDIR=/app

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

# Base FrankenPHP image
FROM frankenphp_upstream AS frankenphp_base

WORKDIR ${WORKDIR}

# Init non-root user
ARG USER=www-data

# Add build arguments for user and group IDs
ARG USER_ID=1000
ARG GROUP_ID=1000

ENV HOME=/home/${USER}
ENV XDG_CONFIG_HOME=$HOME/.config XDG_DATA_HOME=$HOME/.local/share XDG_RUNTIME_DIR=$HOME/.local/run XDG_CACHE_HOME=$HOME/.cache

# Remove default user and group
RUN deluser ${USER} || true \
    && delgroup ${USER} || true

# Create new user and group with the specified IDs, if they don't already exist
RUN if ! getent group ${GROUP_ID} ; then groupadd -g ${GROUP_ID} www-data ; else groupadd -g 9999 www-data ; fi \
    && if ! id -u ${USER_ID} > /dev/null 2>&1 ; then useradd -u ${USER_ID} -ms /bin/bash -g www-data www-data ; fi

# Run frankenphp as non-root user and add permissions
RUN setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    mkdir -p /data/caddy /config/caddy /data/mercure; \
    chown -R ${USER}:${USER} /data/caddy /config/caddy /data/mercure

RUN mkdir -p $XDG_CONFIG_HOME $XDG_DATA_HOME $XDG_RUNTIME_DIR $XDG_CACHE_HOME \
    && chown -R ${USER}:${USER} $XDG_CONFIG_HOME $XDG_DATA_HOME $XDG_RUNTIME_DIR $XDG_CACHE_HOME

RUN mkdir -p /autorun \
    && chown -R ${USER}:${USER} /autorun

# persistent / runtime deps
# hadolint ignore=DL3008
RUN apt-get update && apt-get install -y --no-install-recommends \
        acl \
        file \
        gettext \
        git \
        gosu \
    ;

RUN set -eux; \
    install-php-extensions \
        @composer \
        apcu \
        intl \
        opcache \
        zip \
        pdo_mysql \
        redis \
    ;

ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_MEMORY_LIMIT=-1

ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

###> recipes ###
###> doctrine/doctrine-bundle ###
###< doctrine/doctrine-bundle ###
###< recipes ###

COPY --link --chown=${USER_ID}:${GROUP_ID} .docker/php/conf.d/common $PHP_INI_DIR/app.conf.d/
COPY --link --chown=${USER_ID}:${GROUP_ID} --chmod=755 .docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link --chown=${USER_ID}:${GROUP_ID} .docker/php/Caddyfile /etc/caddy/Caddyfile

USER ${USER}

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

# Dev FrankenPHP image
FROM frankenphp_base AS frankenphp_dev

USER root

COPY --link --chown=${USER_ID}:${GROUP_ID} .docker/php/conf.d/dev $PHP_INI_DIR/app.conf.d/
COPY --link --chown=${USER_ID}:${GROUP_ID} --chmod=755 .docker/php/docker-entrypoint-dev.sh /usr/local/bin/docker-entrypoint

ENV XDEBUG_MODE=off
ENV PHPSTAN_PRO_WEB_PORT=11115
EXPOSE 11115

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
    install-php-extensions \
        xdebug \
    ;

RUN apt-get update \
    && apt-get install -y fish

COPY --chown=${USER_ID}:${GROUP_ID} .docker/php/autorun/dev /autorun/
RUN chmod +x /autorun/*

USER ${USER}

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]

# Prod FrankenPHP image
FROM frankenphp_base AS frankenphp_prod

USER root

ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV APP_RUNTIME="Runtime\FrankenPhpSymfony\Runtime"
ENV MAX_REQUESTS=200

# Cleanup
RUN apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link --chown=${USER_ID}:${GROUP_ID} .docker/php/conf.d/prod $PHP_INI_DIR/app.conf.d/
COPY --link --chown=${USER_ID}:${GROUP_ID} .docker/php/worker.Caddyfile /etc/caddy/worker.Caddyfile

COPY --chown=${USER_ID}:${GROUP_ID} .docker/php/autorun/prod /autorun/
RUN chmod +x /autorun/*

USER ${USER}

# prevent the reinstallation of vendors at every changes in the source code
COPY --link --chown=${USER_ID}:${GROUP_ID} ./composer.* ./symfony.* ./
RUN set -eux; \
    composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy sources
COPY --link --chown=${USER_ID}:${GROUP_ID} . ./

RUN rm -Rf .docker/

RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer dump-env prod; \
    php bin/console cache:clear; \
    php bin/console assets:install public; \
    chmod +x bin/console; sync;
