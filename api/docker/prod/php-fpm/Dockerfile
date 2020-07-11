FROM php:7.4-cli-alpine AS builder

RUN apk add --no-cache postgresql-dev \
	&& docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
	&& docker-php-ext-install pdo_pgsql opcache

RUN apk add --no-cache --virtual .deps imagemagick imagemagick-libs imagemagick-dev
ENV APCU_VERSION 5.1.8
RUN set -xe \
	&& apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
	&& pecl install imagick \
	&& docker-php-ext-enable imagick

RUN apk add --no-cache unzip

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet \
	&& composer global require hirak/prestissimo --no-plugins --no-scripts \
	&& rm -rf /root/.composer/cache

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-dev --prefer-dist --no-progress --no-suggest --no-scripts --optimize-autoloader \
	&& rm -rf /root/.composer/cache

##########

FROM php:7.4-fpm-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql opcache

RUN apk add --no-cache --virtual .deps imagemagick imagemagick-libs imagemagick-dev
ENV APCU_VERSION 5.1.8
RUN set -xe \
	&& apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
	&& pecl install imagick \
	&& docker-php-ext-enable imagick

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

COPY ./docker/prod/nginx/conf.d /usr/local/etc/php/conf.d

WORKDIR /app

COPY --from=builder /app ./

COPY ./ ./
