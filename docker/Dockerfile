FROM php:5.6-apache

# mysqli/pdo_mysql compile from the source already bundled in this image
# against the built-in mysqlnd driver - no apt-get / package mirrors needed
# (ubuntu:trusty's mirrors are gone, this sidesteps that entirely).
RUN docker-php-ext-install mysql mysqli pdo_mysql \
  && docker-php-ext-enable opcache \
  && a2enmod rewrite

# config to enable .htaccess
ADD apache_default /etc/apache2/sites-available/000-default.conf

# Apache listens on 8080: the VPS nginx (host network) already owns 80/443
RUN sed -i 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf

ADD run.sh /run.sh
RUN chmod 755 /run.sh
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

EXPOSE 8080
CMD ["/run.sh"]
