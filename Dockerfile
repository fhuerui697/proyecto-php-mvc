FROM php:8.4-apache

RUN docker-php-ext-install pdo_mysql

# Igualar los IDs de www-data a los del usuario que edita ./src en Ubuntu.
ARG HOST_UID=1000
ARG HOST_GID=1000
RUN groupmod -o -g "${HOST_GID}" www-data \
 && usermod -o -u "${HOST_UID}" -g www-data www-data
