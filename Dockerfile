FROM php:8.1.0alpha3-fpm-alpine

RUN set -ex \
	&& docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php --  --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
