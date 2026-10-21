FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libonig-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mysqli mbstring \
    && sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/

EXPOSE 10000
