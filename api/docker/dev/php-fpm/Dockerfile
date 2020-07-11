FROM php:7.4-fpm-alpine

RUN apk add --no-cache postgresql-dev \
	&& docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
	&& docker-php-ext-install pdo_pgsql

RUN apk add --no-cache --virtual .deps imagemagick imagemagick-libs imagemagick-dev
ENV APCU_VERSION 5.1.8
RUN set -xe \
	&& apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
	&& pecl install imagick \
	&& docker-php-ext-enable imagick

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./conf.d /usr/local/etc/php/conf.d

WORKDIR /app
