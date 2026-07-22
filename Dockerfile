FROM php:8.3-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV NODE_OPTIONS="--max-old-space-size=256"

RUN apk add --no-cache \
    git curl zip unzip bash \
    oniguruma-dev libxml2-dev libzip-dev \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring xml zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Instaliraj PHP pakete bez post-install skripti
COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Kopiraj SVE fajlove
COPY . .

# Kreiraj OBAVEZNE direktorijume pre package:discover
RUN mkdir -p \
    bootstrap/cache \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    && chmod -R 775 bootstrap/cache storage

# Sada pokrecemo package:discover (bootstrap/cache postoji)
RUN php artisan package:discover --ansi

# Build Vite assets
RUN npm install --no-optional && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
