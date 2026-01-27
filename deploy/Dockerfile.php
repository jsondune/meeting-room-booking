# Meeting Room Booking System - PHP-FPM Container
# Optimized for Yii2 with all required extensions

FROM php:8.2-fpm-alpine

LABEL maintainer="BIzAI"
LABEL description="PHP-FPM container for Meeting Room Booking System"
LABEL version="1.0"

# Build arguments
ARG APP_ENV=production
ARG COMPOSER_VERSION=2.6

# Environment variables
ENV APP_ENV=${APP_ENV}
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES=20000
ENV PHP_OPCACHE_MEMORY_CONSUMPTION=256
ENV PHP_OPCACHE_JIT_BUFFER_SIZE=100M

# Install system dependencies
RUN apk add --no-cache \
    # Build dependencies
    $PHPIZE_DEPS \
    # Required libraries
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    curl-dev \
    openssl-dev \
    # Runtime utilities
    git \
    unzip \
    curl \
    supervisor \
    dcron \
    # For healthcheck
    fcgi \
    # For image processing
    imagemagick \
    imagemagick-dev \
    # For MySQL client
    mysql-client \
    # For Redis
    hiredis-dev

# Install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        intl \
        mbstring \
        xml \
        curl \
        opcache \
        bcmath \
        exif \
        pcntl \
        sockets

# Install PECL extensions
RUN pecl install redis-6.0.2 \
    && pecl install imagick \
    && pecl install apcu \
    && docker-php-ext-enable redis imagick apcu

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --version=${COMPOSER_VERSION} \
        --install-dir=/usr/local/bin \
        --filename=composer

# Create application user
RUN addgroup -g 1000 app \
    && adduser -u 1000 -G app -s /bin/sh -D app

# Create required directories
RUN mkdir -p /var/log/php \
    && mkdir -p /var/run/php \
    && mkdir -p /app/runtime \
    && mkdir -p /app/web/assets \
    && mkdir -p /app/frontend/runtime \
    && mkdir -p /app/frontend/web/assets \
    && mkdir -p /app/backend/runtime \
    && mkdir -p /app/backend/web/assets \
    && mkdir -p /app/api/runtime

# Copy PHP configuration files
COPY deploy/php/php.ini /usr/local/etc/php/php.ini
COPY deploy/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY deploy/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /app

# Copy application files
COPY --chown=app:app . /app

# Install dependencies (production)
RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction --no-progress; \
    else \
        composer install --optimize-autoloader --no-interaction --no-progress; \
    fi

# Set permissions
RUN chown -R app:app /app \
    && chown -R app:app /var/log/php \
    && chown -R app:app /var/run/php \
    && chmod -R 775 /app/runtime \
    && chmod -R 775 /app/frontend/runtime \
    && chmod -R 775 /app/backend/runtime \
    && chmod -R 775 /app/api/runtime \
    && chmod -R 775 /app/web/assets \
    && chmod -R 775 /app/frontend/web/assets \
    && chmod -R 775 /app/backend/web/assets

# Health check script
COPY deploy/php/healthcheck.sh /usr/local/bin/healthcheck.sh
RUN chmod +x /usr/local/bin/healthcheck.sh

# Switch to non-root user
USER app

# Expose PHP-FPM port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

# Start PHP-FPM
CMD ["php-fpm", "-F"]
