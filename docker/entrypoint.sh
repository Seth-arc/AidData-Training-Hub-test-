#!/bin/sh
set -e

: "${PORT:=80}"
echo "Nginx will listen on port: $PORT"

# Substitute environment variables in Nginx config
envsubst '$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp
mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

php-fpm &
nginx -g "daemon off;"
ls -l /var/www/html
ls -l /var/www/html/wp-admin