# --- Base PHP Environment ---
FROM php:8.4-fpm AS base

WORKDIR /var/www

# Install core extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd opcache zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP Opcache & JIT
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.jit_buffer_size=128M'; \
    echo 'opcache.jit=tracing'; \
    echo 'upload_max_filesize=64M'; \
    echo 'post_max_size=64M'; \
    echo 'memory_limit=512M'; \
    } > /usr/local/etc/php/conf.d/symfony-optimizations.ini

# --- Development (Windows Specific) ---
FROM base AS development

# This is what made your previous setup work!
# It changes the PHP-FPM pool config to allow root.
RUN sed -i 's/user = www-data/user = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/group = www-data/group = root/g' /usr/local/etc/php-fpm.d/www.conf

# Development specific INI overrides
RUN { \
    echo 'display_errors=On'; \
    echo 'display_startup_errors=On'; \
    echo 'error_reporting=E_ALL'; \
    echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/dev-overrides.ini

# Run as root for development to fix the WSL/Windows file sync issues
USER root

EXPOSE 9000
CMD ["php-fpm", "-R"]

# --- Production ---
FROM base AS production

# Set production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Production hardening
RUN { \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'display_errors=Off'; \
    } > /usr/local/etc/php/conf.d/prod-overrides.ini

# Copy your actual source code
COPY ./src /var/www

# Symfony-specific directory setup
RUN mkdir -p /var/www/var/cache /var/www/var/log && \
    chown -R www-data:www-data /var/www/var && \
    chmod -R 775 /var/www/var

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

USER www-data

EXPOSE 9000
CMD ["php-fpm"]