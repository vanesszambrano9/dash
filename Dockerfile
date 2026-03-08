FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev libxml2-dev libonig-dev \
    curl unzip git nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip intl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080
