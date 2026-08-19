FROM php:5.6-apache

# mysqli/pdo_mysql compile from the source already bundled in this image
# against the built-in mysqlnd driver - no apt-get / package mirrors needed
# (ubuntu:trusty's mirrors are gone, this sidesteps that entirely).
RUN docker-php-ext-install mysql mysqli pdo_mysql \
  && docker-php-ext-enable opcache \
  && a2enmod rewrite

# The zip extension (needed by PHPExcel to write .xlsx) does need a package -
# libzip-dev - and this image's Debian stretch is EOL, so deb.debian.org/
# security.debian.org are gone the same way ubuntu:trusty's were. Point at
# archive.debian.org instead (no stretch-updates suite there, and its Release
# files are expired/unsigned, hence check-valid-until=no and [trusted=yes]).
RUN echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until \
  && printf 'deb [trusted=yes] http://archive.debian.org/debian stretch main\ndeb [trusted=yes] http://archive.debian.org/debian-security stretch/updates main\n' > /etc/apt/sources.list \
  && apt-get update \
  && apt-get install -y --no-install-recommends libzip-dev \
  && docker-php-ext-install zip \
  && rm -rf /var/lib/apt/lists/*

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
