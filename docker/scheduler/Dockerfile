# Dockerfile
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    bash \
    git

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip intl

# Install Composer
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application files
COPY . .

# Optional: Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
