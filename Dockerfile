FROM php:8.1-apache
RUN apt-get update \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

WORKDIR /var/www/html
