FROM php:8.3-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1
# Ogranichi Node.js heap na 256 MB da sprecimo OOM na Railway-u
ENV NODE_OPTIONS="--max-old-space-size=256"

# Alpine apk je 10x laxsi od Debian apt-get
RUN apk add --no-cache \
    git curl zip unzip bash \
    oniguruma-dev libxml2-dev libzip-dev \
    nodejs npm \
    && docker-php-ext-install pdo_mysql mbstring xml zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# PHP zavisnosti prvo (brzi cache sloj)
COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Kopiraj sve fajlove
COPY . .

# Build Vite assets
RUN npm ci --no-optional && npm run build

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
