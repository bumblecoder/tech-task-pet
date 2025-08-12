FROM php:8.3-fpm-bullseye

ARG APP_ENV=prod
ARG XDEBUG=0
ARG USER_ID=1000
ARG GROUP_ID=1000

ENV USER_ID=${USER_ID} \
    GROUP_ID=${GROUP_ID} \
    USERNAME=php

WORKDIR /code

RUN apt-get update -qq && apt-get install -y --no-install-recommends \
    default-mysql-client \
    libsqlite3-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    netcat \
 && docker-php-ext-configure intl \
 && docker-php-ext-install mysqli pdo_mysql pdo_sqlite intl zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN if [ "$APP_ENV" = "dev" ] || [ "$APP_ENV" = "test" ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug ; \
fi

COPY --from=composer:2.5.4 /usr/bin/composer /usr/local/bin/composer

COPY --chown=${USER_ID}:${GROUP_ID} ./code /code

RUN composer install --no-scripts --no-interaction --no-progress

RUN groupadd -g ${GROUP_ID} ${USERNAME} \
 && useradd -u ${USER_ID} -g ${GROUP_ID} -m -s /bin/bash ${USERNAME}

RUN chown -R ${USER_ID}:${GROUP_ID} \
    /usr/local/etc \
    /usr/local/sbin/php-fpm \
    /usr/local/bin/composer

USER ${USERNAME}

EXPOSE 9000
STOPSIGNAL SIGTERM
CMD ["php-fpm", "--nodaemonize"]
