#!/bin/sh
set -e

: "${PORT:=80}"
echo "Nginx will listen on port: $PORT"

# Substitute environment variables in Nginx config
envsubst '$PORT' < /etc/nginx/conf.d/wordpress.conf > /etc/nginx/conf.d/wordpress.conf.tmp
mv /etc/nginx/conf.d/wordpress.conf.tmp /etc/nginx/conf.d/wordpress.conf

# Test nginx configuration
nginx -t

php-fpm &
ls -l /var/www/html
ls -l /var/www/html/wp-admin
nginx -g "daemon off;"