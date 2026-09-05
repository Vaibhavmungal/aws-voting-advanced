# ==============================================================================
# VoteSecure — Advanced Online Voting Platform
# Production-grade Apache + PHP Container
# ==============================================================================

FROM php:8.2-apache

# Set metadata
LABEL maintainer="Vaibhav Mungal <vaibhavmungal@gmail.com>"
LABEL description="Production Docker image for VoteSecure Online Voting System"

# Environment variables
ENV DEBIAN_FRONTEND=noninteractive \
    APACHE_DOCUMENT_ROOT=/var/www/html

# Install system dependencies, required build libraries, and embedded MariaDB server
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    zip \
    unzip \
    mariadb-server \
    mariadb-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo \
        pdo_mysql \
        gd \
        opcache \
        mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache: enable rewrite and security headers
RUN a2enmod rewrite headers

# Configure custom PHP settings and socket communication
RUN { \
    echo 'expose_php = Off'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT'; \
    echo 'upload_max_filesize = 25M'; \
    echo 'post_max_size = 30M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 60'; \
    echo 'date.timezone = UTC'; \
    echo 'opcache.enable = 1'; \
    echo 'opcache.memory_consumption = 128'; \
    echo 'opcache.interned_strings_buffer = 8'; \
    echo 'opcache.max_accelerated_files = 4000'; \
    echo 'opcache.revalidate_freq = 2'; \
    echo 'opcache.fast_shutdown = 1'; \
    echo 'pdo_mysql.default_socket = /var/run/mysqld/mysqld.sock'; \
    echo 'mysqli.default_socket = /var/run/mysqld/mysqld.sock'; \
    echo 'mysql.default_socket = /var/run/mysqld/mysqld.sock'; \
} > /usr/local/etc/php/conf.d/custom-php.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Setup entrypoint script, upload permissions, and MySQL socket directories
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /var/www/html/uploads /var/run/mysqld /var/lib/mysql /tmp \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads \
    && chown -R mysql:mysql /var/lib/mysql /var/run/mysqld

# Expose HTTP port
EXPOSE 80

# Health check to monitor container availability
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1

# Start container via self-initializing entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
