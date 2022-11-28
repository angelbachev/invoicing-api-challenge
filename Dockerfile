FROM php:8.1-fpm

ARG UID=1000
ARG GID=1000
ARG CS_FIXER_VERSION=3.9.2
ARG SECURITY_CHECKER_VERSION=1.2.0

RUN DEBIAN_FRONTEND=noninteractive apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        git \
        libzip-dev \
        unzip \
    && pecl install pcov \
    && docker-php-ext-install -j$(nproc) \
        zip \
    && docker-php-ext-enable pcov \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v${CS_FIXER_VERSION}/php-cs-fixer.phar -o php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

RUN curl -L https://github.com/fabpot/local-php-security-checker/releases/download/v${SECURITY_CHECKER_VERSION}/local-php-security-checker_${SECURITY_CHECKER_VERSION}_linux_amd64 -o local-php-security-checker \
    && chmod a+x local-php-security-checker \
    && mv local-php-security-checker /usr/local/bin/local-php-security-checker

RUN groupadd -g $GID invoices \
    && useradd -d /home/invoices -s /bin/bash -u $UID -g $GID invoices \
    && mkdir -p /home/invoices/app \
    && chown -R invoices:invoices /home/invoices

USER invoices

WORKDIR /home/invoices/app

ENTRYPOINT ["./entrypoint.sh"]

#CMD ["php-fpm"]

