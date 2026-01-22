# Use official PHP-FPM image with Apache
FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
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

# Disable conflicting MPM modules and enable the correct one
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# Enable Apache modules
RUN a2enmod rewrite expires headers

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

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]
