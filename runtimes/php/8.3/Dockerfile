FROM php:8.3-fpm-alpine

RUN apk --update --no-cache add git zip unzip tzdata git curl icu libintl libldap composer \
        && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS linux-headers \
    # Extensions: soap bcmath exif intl opcache ldap pdo_mysql sockets
        && apk add --no-cache --virtual .exts-deps libxml2-dev openldap-dev icu-dev \
        && docker-php-ext-configure bcmath \
        && docker-php-ext-install soap bcmath exif intl opcache ldap pdo_mysql sockets \
        && apk del .exts-deps \
    # Extensions: pcov igbinary msgpack
        && pecl install pcov && docker-php-ext-enable pcov \
        && pecl install igbinary && docker-php-ext-enable igbinary \
        && pecl install msgpack && docker-php-ext-enable msgpack \
    # GD with support: freetype, png, jpeg, webp, avif
        && apk --update --no-cache add libpng freetype libjpeg-turbo libwebp freetype libavif \
        && apk --update --no-cache add --virtual .gd-deps libpng-dev freetype-dev libjpeg-turbo-dev libwebp-dev libavif-dev \
        && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype --with-avif \
        && docker-php-ext-install gd \
        && apk del .gd-deps \
    # Postgress
        && apk --update --no-cache add php83-pdo_pgsql \
        && apk --update --no-cache add --virtual .pg-deps postgresql-dev \
        && docker-php-ext-install pdo_pgsql \
        && apk del .pg-deps \
    # Zip
        && apk --update --no-cache add libzip-dev \
        && docker-php-ext-configure zip \
        && docker-php-ext-install zip \
    # Imap
        && apk add --no-cache imap-dev \
        && apk add --no-cache --virtual .imap-deps krb5-dev openssl-dev \
        && docker-php-ext-configure imap --with-imap --with-kerberos --with-imap-ssl \
        && docker-php-ext-install imap \
        && apk del .imap-deps \
    # Imagick
        && apk add --no-cache imagemagick-dev \
        && curl -L -o /tmp/imagick.tar.gz https://pecl.php.net/get/imagick-3.7.0.tgz \
        && tar --strip-components=1 -xf /tmp/imagick.tar.gz \
        && phpize \
        && ./configure \
        && make \
        && make install \
        && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/docker-php-ext-imagick.ini \
        && rm -rf /tmp/* \
    # Memcache \
        && apk add --no-cache libmemcached \
        && apk add --no-cache --virtual .memcached-deps libmemcached-dev zlib-dev cyrus-sasl-dev \
        && pecl install memcached \
        && docker-php-ext-enable memcached \
        && apk del .memcached-deps \
    # Redis
        && apk add --no-cache --virtual .redis-deps pcre-dev \
        && pecl install redis \
        && docker-php-ext-enable redis \
        && apk del .redis-deps \
    # XDebug (Notice: The linux-headers installed in .phpize-deps)
        #&& apk add --no-cache --virtual .xdebug-deps linux-headers \
        && pecl install xdebug \
        && docker-php-ext-enable xdebug \
        #&& apk del -f .xdebug-deps \
    # Clear image
        && apk del .phpize-deps \
        && rm -rf /tmp/pear \
    #&& rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

# Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure PHP
COPY ./php.ini /usr/local/etc/php/conf.d/imhotep.ini

# Configure XDebug
ENV PHP_IDE_CONFIG 'serverName=imhotep.debug'
COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www