# Use whatever version you prefer; 8.3 is current, 8.1 also works.
FROM php:8.3-apache

# System deps for pdo_pgsql (and useful extras)
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
       libpq-dev \
  && docker-php-ext-install -j"$(nproc)" pdo_pgsql \
  && a2enmod rewrite \
  && rm -rf /var/lib/apt/lists/*

# (Optional) set Apache docroot if your app isn't in /var/www/html
# ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
#     -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy your app
COPY . /var/www/html
