FROM php:8.5.5-cli-trixie AS base

RUN docker-php-ext-install pdo_mysql && \
    curl -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar && \
    chmod +x /usr/local/bin/composer && \
    mkdir /app && \
    mkdir /app/vendor && \
    chown -R www-data:www-data /app

RUN apt-get -y update && \
    apt-get install -y --no-install-recommends procps && \
    apt-get install -y netcat-traditional && \
    apt-get install -y unzip && \
    apt-get -y autoremove && \
    apt-get clean

WORKDIR /app

USER www-data:www-data



FROM base AS dev

USER root:root

RUN apt-get install -y rsync && \
    mkdir -p /home/www-data/.composer && \
    chown -R www-data:www-data /home/www-data

USER www-data:www-data

CMD /bin/sh
