FROM php:8-cli-alpine
COPY php/zzz-fpm.conf /usr/local/etc/php-fpm.d/zzz-fpm.conf

ARG UID
ARG GID
RUN echo $UID $GID
RUN apk --update --no-cache add \
    curl \
    git

RUN apk --update --no-cache add autoconf g++ make && \
    pecl install -f xdebug && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable xdebug pdo_mysql && \
    apk del --purge autoconf g++ make

RUN echo "xdebug.mode=coverage" >> "$PHP_INI_DIR/php.ini-development"
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN addgroup -S -g ${GID} app \
    && adduser -S -D -u ${UID} -s /sbin/nologin -h /home/app -G app app \
    && chown -R app:app /home/app \
    && chmod -R g+w /home/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

USER app
WORKDIR /app
