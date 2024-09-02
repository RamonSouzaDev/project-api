FROM php:8.3-fpm

# Set Environment Variables
ENV DEBIAN_FRONTEND=noninteractive

ARG user=developer
ARG uid=1000

# Install essential packages
RUN apt-get update -y && \
    apt-get install -y \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libwebp-dev \
    libxpm-dev \
    libmcrypt-dev \
    libonig-dev \
    libzip-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev && \
    docker-php-ext-install zip

# Install additional PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

COPY .env.example .env
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY composer.json composer.lock ./

# Configure environment variables in .env file
RUN echo "DB_CONNECTION=mysql" > .env \
    && echo "DB_HOST=db" >> .env \
    && echo "DB_PORT=3306" >> .env \
    && echo "DB_DATABASE=banking" >> .env \
    && echo "DB_USERNAME=root" >> .env \
    && echo "DB_PASSWORD=root" >> .env

COPY . .

# Create the SQLite database file
RUN touch database/database.sqlite \
    && chown -R $user:$user database/database.sqlite

USER root

ENV XDEBUG_MODE=coverage

RUN mkdir -p coverage \
    && chown -R $user:$user coverage \
    && chmod -R 755 coverage

USER $user
