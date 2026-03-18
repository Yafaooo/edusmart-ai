# PHP Stage
FROM php:8.3-fpm-alpine as php-stage

RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    postgresql-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --optimize-autoloader --no-dev
RUN php artisan key:generate || true

# Node Stage (Vite Build)
FROM node:20-alpine as node-stage
WORKDIR /var/www/html
COPY --from=php-stage /var/www/html .
RUN npm install && npm run build

# Final Stage
FROM php-stage
COPY --from=node-stage /var/www/html/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config
COPY ./nginx.conf /etc/nginx/nginx.conf

EXPOSE 10000

CMD php artisan config:cache && php artisan route:cache && php-fpm -D && nginx -g 'daemon off;'
