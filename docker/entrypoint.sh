#!/bin/sh
set -e

: "${PORT:=80}"
echo "Nginx will listen on port: $PORT"

# Ensure required directories exist
mkdir -p /var/log/nginx /var/run

# Substitute PORT in Nginx config (using sed to avoid envsubst issues)
sed "s/\${PORT}/${PORT}/g" /etc/nginx/conf.d/wordpress.conf > /etc/nginx/conf.d/wordpress.conf.tmp
mv /etc/nginx/conf.d/wordpress.conf.tmp /etc/nginx/conf.d/wordpress.conf

# Debug: Show nginx config
echo "=== Nginx configuration ==="
cat /etc/nginx/conf.d/wordpress.conf

# Verify WordPress files exist
echo "=== Checking WordPress installation ==="
ls -la /var/www/html/ | head -20
test -f /var/www/html/index.php && echo "✓ index.php exists" || echo "✗ index.php missing!"
test -f /var/www/html/wp-config.php && echo "✓ wp-config.php exists" || echo "✗ wp-config.php missing!"

# Start PHP-FPM in background
echo "=== Starting PHP-FPM ==="
php-fpm &
PHP_FPM_PID=$!

# Wait for PHP-FPM to be ready
echo "=== Waiting for PHP-FPM to start ==="
sleep 3

# Verify PHP-FPM is running
if ! kill -0 $PHP_FPM_PID 2>/dev/null; then
    echo "ERROR: PHP-FPM failed to start!"
    exit 1
fi
echo "✓ PHP-FPM is running (PID: $PHP_FPM_PID)"

# Test nginx configuration
echo "=== Testing nginx configuration ==="
nginx -t || exit 1

# Enable PHP error logging
echo "=== Configuring PHP error logging ==="
echo "error_log = /var/log/php-fpm-error.log" >> /usr/local/etc/php-fpm.conf
echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf
echo "php_admin_value[error_log] = /var/log/php-fpm-error.log" >> /usr/local/etc/php-fpm.d/www.conf

# Tail both nginx and PHP error logs
tail -f /var/log/nginx/error.log /var/log/php-fpm-error.log 2>/dev/null &

# Start Nginx in foreground
echo "=== Starting Nginx ==="
exec nginx -g "daemon off;"