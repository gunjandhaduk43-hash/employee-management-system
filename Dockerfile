FROM php:8.4-cli-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    sqlite-dev \
    mariadb-connector-c-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite intl bcmath opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies with ignore-platform-reqs safeguard
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=php+

# Set up environment and permissions
RUN cp -n .env.example .env || true \
    && touch database/database.sqlite \
    && php artisan key:generate --force \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache database

# Render passes the PORT environment variable (default 10000)
ENV PORT=10000
EXPOSE 10000

# Start script: run migrations, seed database, cache configs, and start server
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
