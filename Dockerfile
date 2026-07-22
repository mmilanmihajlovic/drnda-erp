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

# Korak 1: instaliraj PHP pakete BEZ post-install skripti
# (artisan i bootstrap/ jos ne postoje u ovom trenutku)
COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Korak 2: sada kopiraj SVE fajlove (artisan, bootstrap/, config/, ...)
COPY . .

# Korak 3: pokrecemo post-install skripte SAD kad su svi fajlovi prisutni
RUN php artisan package:discover --ansi

# Korak 4: kreiraj storage direktorijume
RUN mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Korak 5: build Vite assets
RUN npm install --no-optional && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
