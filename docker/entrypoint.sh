#!/bin/sh
set -e

: "${PORT:=80}"
echo "Nginx will listen on port: $PORT"

# Substitute environment variables in Nginx config
envsubst '$PORT' < /etc/nginx/sites-available/wordpress.conf > /etc/nginx/sites-available/wordpress.conf.tmp
mv /etc/nginx/sites-available/wordpress.conf.tmp /etc/nginx/sites-available/wordpress.conf

php-fpm &
ls -l /var/www/html
ls -l /var/www/html/wp-admin
nginx -g "daemon off;"