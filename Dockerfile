# 1) Stage Node: compila assets (Vite) -> public/build
FROM node:20-alpine AS node_builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# 2) Stage Composer: instala vendor/
FROM composer:2 AS composer_builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# 3) Stage final PHP-FPM (ajuste extensões se precisar)
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libpng libjpeg-turbo freetype icu-data-full icu-libs \
 && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    icu-dev libpng-dev libjpeg-turbo-dev freetype-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql gd intl \
 && apk del .build-deps

WORKDIR /var/www/html

# Código + vendor
COPY --from=composer_builder /app ./
# Assets compilados (public/build)
COPY --from=node_builder /app/public/build ./public/build

# Se faltar APP_KEY/VARs no build, rode estes no pre-deploy do provedor:
# RUN php artisan storage:link || true \
#  && php -d memory_limit=-1 artisan config:cache \
#  && php artisan route:cache \
#  && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]
