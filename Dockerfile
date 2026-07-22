FROM php:8.3-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install ALL deps in one step - using Debian bookworm's native nodejs (v18)
RUN apt-get update -y && apt-get install -y \
    git curl zip unzip \
    libonig-dev libxml2-dev libzip-dev \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring xml zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first (Docker cache optimization)
COPY composer.json ./
COPY composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Copy rest of app + build assets
COPY . .
RUN npm install && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
