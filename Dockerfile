FROM php:8.3-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV NODE_OPTIONS="--max-old-space-size=256"

# Alpine apk je mnogo laxsi od Debian apt-get
RUN apk add --no-cache \
    git curl zip unzip bash \
    oniguruma-dev libxml2-dev libzip-dev \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring xml zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# PHP zavisnosti (cache sloj)
COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Kopiraj sve fajlove
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

# Build Vite assets (npm install umesto npm ci jer nema package-lock.json)
RUN npm install --no-optional && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
