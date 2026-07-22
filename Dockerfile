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

COPY composer.json composer.lock* ./
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

COPY . .

RUN mkdir -p \
    bootstrap/cache \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    && chmod -R 777 bootstrap/cache storage

RUN php artisan package:discover --ansi

RUN npm install && npm run build

EXPOSE 8080

# Pokrecemo migrate odmah u CMD (ne u releaseCommand) da vidimo greske u logovima
# APP_KEY mora biti setovan u Railway Variables
CMD ["sh", "-c", "echo Starting... && echo PORT=$PORT && php artisan config:clear && php artisan migrate --force 2>&1 && php artisan db:seed --force 2>&1 && php artisan storage:link --force 2>&1 && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
