FROM php:8.3-apache 

WORKDIR /var/www/html/

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y apt-utils && \
    a2enmod rewrite && \
    apt-get install -y \
        libmcrypt-dev \
        libicu-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libxml2-dev \
        libldb-dev \
        libldap2-dev \
        libssl-dev \
        libxslt-dev \
        libpq-dev \
        libzip-dev \
        libsqlite3-dev \
        libsqlite3-0 \
        libc-client-dev \
        libkrb5-dev \
        curl \
        zip && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install zip && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-enable pdo_mysql mysqli && \
    curl -O https://nodejs.org/download/release/v20.9.0/node-v20.9.0-linux-x64.tar.gz && \
    tar -xf node-v20.9.0-linux-x64.tar.gz -C /usr/local --strip-components=1 && \
    rm node-v20.9.0-linux-x64.tar.gz && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*