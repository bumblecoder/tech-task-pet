FROM php:8.2-fpm-bullseye

ARG APP_ENV
ARG XDEBUG
ARG USER_ID
ARG GROUP_ID
ENV USER_ID=${USER_ID:-1000} \
    GROUP_ID=${GROUP_ID:-1000} \
    USERNAME="php"
    
WORKDIR /code
    
RUN ln -snf /usr/share/zoneinfo/America/Los_Angeles /etc/localtime && echo America/Los_Angeles > /etc/timezone

RUN apt-get update -qq --fix-missing \
    && apt-get install -qq -y --no-install-recommends \
    default-mysql-client \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install mysqli pdo_mysql intl zip

RUN if [ "${APP_ENV}" = "dev" ] || [ "${APP_ENV}" = "test" ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug \
;fi

COPY --from=composer:2.5.4 /usr/bin/composer /usr/local/bin/composer

COPY ./code /code

RUN composer install --no-scripts

# create custom user for php-fpm service
RUN groupadd -g ${GROUP_ID} ${USERNAME} \
    && useradd -l -u ${USER_ID} -g ${GROUP_ID} -m ${USERNAME} \
    && chown --changes --silent --no-dereference --recursive ${USER_ID}:${GROUP_ID} \
    /run \
    /var/log \
    /usr/local/lib \
    /usr/local/etc \
    /usr/local/sbin/php-fpm \
    /code \
    /usr/local/bin/composer 2>&1 >/dev/null

USER ${USERNAME}

STOPSIGNAL SIGTERM

EXPOSE 9000

CMD ["/usr/local/sbin/php-fpm","--nodaemonize"]
    