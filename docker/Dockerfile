FROM ubuntu:trusty
MAINTAINER Fernando Mayo <fernando@tutum.co>, Feng Honglin <hfeng@tutum.co>

# ubuntu:trusty is EOL, its packages were moved off the regular mirrors
RUN sed -i \
  -e 's|archive.ubuntu.com|old-releases.ubuntu.com|g' \
  -e 's|security.ubuntu.com|old-releases.ubuntu.com|g' \
  /etc/apt/sources.list

# Install packages (no mysql-server / phpmyadmin: DB now lives outside the container)
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
  apt-get -y install apache2 libapache2-mod-php5 php5-mysql pwgen php-apc php5-mcrypt && \
  echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Add image configuration and scripts
ADD start-apache2.sh /start-apache2.sh
ADD run.sh /run.sh
RUN chmod 755 /*.sh

# config to enable .htaccess
ADD apache_default /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Apache listens on 8080: the VPS nginx (host network) already owns 80/443
RUN sed -i 's/^Listen 80$/Listen 8080/' /etc/apache2/ports.conf

#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

EXPOSE 8080
CMD ["/run.sh"]
