#!/bin/bash

sed -ri -e "s/^upload_max_filesize.*/upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}/" \
    -e "s/^post_max_size.*/post_max_size = ${PHP_POST_MAX_SIZE}/" "$PHP_INI_DIR/php.ini"

# CakePHP needs these writable; the host repo doesn't always have them created
mkdir -p /var/www/html/app/tmp/cache/models /var/www/html/app/tmp/cache/persistent \
    /var/www/html/app/tmp/cache/views /var/www/html/app/tmp/sessions /var/www/html/app/tmp/logs
chmod -R 777 /var/www/html/app/tmp

exec apache2-foreground
