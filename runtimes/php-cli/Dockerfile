FROM php:7.1-cli-alpine

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

# composer
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:1.5 /usr/bin/composer /usr/bin/composer

# from phpspec/phpspec
WORKDIR /tmp

RUN composer selfupdate \
    && composer require "phpspec/phpspec:~2.5.2" --dev \
    && ln -s /tmp/vendor/bin/phpspec /usr/local/bin/phpspec

WORKDIR /app
CMD ["/bin/sh"]