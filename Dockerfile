FROM php:8.2-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS librdkafka-dev && \
    pecl -q install rdkafka && \
    docker-php-ext-enable rdkafka && \
    rm -rf /tmp/* /var/cache/apk/*

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
