FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    nodejs \
    npm \
    zip \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

ENV COMPOSER_PROCESS_TIMEOUT=900

RUN mkdir -p /var/data \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && touch /var/data/database.sqlite \
    && for i in 1 2 3 4 5 6 7 8; do \
        composer install --no-interaction --prefer-source --no-progress --optimize-autoloader --no-dev && break || { \
            if [ "$i" -lt 8 ]; then \
                echo "Composer install failed (attempt $i/8), retrying in $((i * 10))s..."; \
                sleep $((i * 10)); \
            else \
                exit 1; \
            fi; \
        }; \
    done \
    && npm install \
    && npm run build \
    && php artisan view:cache

EXPOSE 8000

CMD ["sh", "./docker-start.sh"]
