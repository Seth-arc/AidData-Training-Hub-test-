#!/bin/sh
set -e

: "${PORT:=80}"
echo "Nginx will listen on port: $PORT"

# Ensure required directories exist
mkdir -p /var/log/nginx /var/run

# Substitute environment variables in Nginx config
envsubst '$PORT' < /etc/nginx/conf.d/wordpress.conf > /etc/nginx/conf.d/wordpress.conf.tmp
mv /etc/nginx/conf.d/wordpress.conf.tmp /etc/nginx/conf.d/wordpress.conf

# Debug: Show nginx config
echo "=== Nginx configuration ==="
cat /etc/nginx/conf.d/wordpress.conf

# Start PHP-FPM in background
php-fpm &

# Wait a moment for PHP-FPM to start
sleep 2

# Start Nginx in foreground
exec nginx -g "daemon off;"