FROM php:8.2-fpm-alpine

# Install dependencies and PHP extensions
RUN apk add --no-cache \
    libldap \
    postgresql-dev \
    && apk add --no-cache --virtual .build-deps \
    openldap-dev \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install ldap pdo pdo_mysql mysqli pdo_pgsql \
    && apk del .build-deps

# Install other required packages
RUN apk add --no-cache \
    curl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY . /app
WORKDIR /app

# Copy the PHP config file
COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

VOLUME ["/app"]