# Use official PHP-FPM image with Nginx
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    gettext-base \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    intl \
    mbstring \
    xml \
    opcache \
    exif

# Set PHP memory limit and configuration
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "max_execution_time = 300" > /usr/local/etc/php/conf.d/execution-time.ini \
    && echo "default_socket_timeout = 300" >> /usr/local/etc/php/conf.d/execution-time.ini

# Copy WordPress files
COPY app/public/ /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Create wp-content directories if they don't exist
RUN mkdir -p /var/www/html/wp-content/uploads \
    /var/www/html/wp-content/cache \
    /var/www/html/wp-content/upgrade \
    && chown -R www-data:www-data /var/www/html/wp-content

# Copy Nginx config and entrypoint
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Nginx + PHP-FPM
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
