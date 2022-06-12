FROM php:7.1-fpm-alpine

MAINTAINER Arash Khajelou <a.khajelou@gmail.com>

# persistent dependencies
RUN set -eux; \
	apk add --no-cache \
		bash \
		sed \
		imagemagick \
		libmcrypt \
		libmcrypt-dev \
		libxml2 \
		libxml2-dev \
		curl \
	;

RUN set -ex; \
	\
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		freetype-dev \
		imagemagick-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		libzip-dev \
	; \
	\
	docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg \
	; \
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		gd \
		mysqli \
		zip \
		mcrypt \
		pdo_mysql \
		soap \
		json \
		simplexml \
		xml \
		bcmath \
	; \
	pecl install imagick-3.4.4; \
	docker-php-ext-enable imagick;

RUN { \
		echo 'error_reporting = E_ALL'; \
		echo 'display_errors = On'; \
		echo 'display_startup_errors = On'; \
		echo 'log_errors = On'; \
		echo 'error_log = /dev/stderr'; \
		echo 'log_errors_max_len = 1024'; \
		echo 'ignore_repeated_errors = On'; \
		echo 'ignore_repeated_source = Off'; \
		echo 'html_errors = On'; \
	} > /usr/local/etc/php/conf.d/error-logging.ini


USER www-data