# Multi-stage build for production-ready Laravel application
FROM php:8.3-fpm AS base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && git config --global --add safe.directory /var/www/html \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy custom PHP configuration
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# --- Development Stage ---
FROM base AS development

# Set environment for development
ENV APP_ENV=local

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader \
    && npm install \
    && npm run build

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]

# --- Production Stage ---
FROM base AS production

# Set environment for production
ENV APP_ENV=production

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Install production dependencies only
RUN composer install --no-dev --no-interaction --optimize-autoloader --classmap-authoritative \
    && npm ci --only=production \
    && npm run build \
    && rm -rf node_modules \
    && rm -rf .git

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel for production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 9000

CMD ["php-fpm"]
