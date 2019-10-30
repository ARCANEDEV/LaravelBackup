FROM php:7-cli-alpine

# Cleaning...
RUN rm -rf /var/cache/apk/* /tmp/*
RUN apk update

# Installing Tools / Libs
RUN apk add --no-cache \
    zip \
    libzip-dev \
    sqlite \
    mysql-client

# Installing PHP Extensions
RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    zip

# Installing COMPOSER
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy PHP config file
COPY config/php-dev.ini /usr/local/etc/php/php.ini
