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

# Kopiramo composer.json + artisan zajedno — artisan mora biti tu
# pre nego sto "composer install" pokrene "@php artisan package:discover"
COPY composer.json composer.lock* artisan ./

RUN composer install --optimize-autoloader --no-dev --no-interaction

# Kopiraj ostale fajlove
COPY . .

# Kreiraj obavezne Laravel storage direktorijume
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Build Vite assets
RUN npm install --no-optional && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
