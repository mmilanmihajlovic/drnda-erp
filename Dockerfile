FROM php:8.3-cli-bookworm

# Sistem zavisnosti
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libxml2-dev libcurl4-openssl-dev libzip-dev \
    zip unzip git curl ca-certificates gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd xml curl zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Kopiraj aplikaciju
COPY . .

# PHP zavisnosti
RUN composer install --no-dev --optimize-autoloader --no-interaction

# JS zavisnosti + build
RUN npm ci && npm run build

# Expose port
EXPOSE 8080

# Start: migrate + seed + serve
# Sve u jednom CMD da bi Railway env vars bili dostupni
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
