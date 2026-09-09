FROM php:7.4-apache

RUN docker-php-ext-install mysqli && a2enmod rewrite
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/apache.conf /etc/apache2/conf-available/sob.conf
RUN a2enconf sob
WORKDIR /var/www/html
