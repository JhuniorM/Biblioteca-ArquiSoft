FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js .
RUN npm run build

FROM php:8.4-cli-alpine AS vendor

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN apk add --no-cache oniguruma libpq \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS oniguruma-dev postgresql-dev \
    && docker-php-ext-install mbstring pdo_pgsql \
    && apk del .build-deps \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache nginx supervisor gettext-envsubst oniguruma libpq \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS oniguruma-dev postgresql-dev \
    && docker-php-ext-install bcmath mbstring opcache pdo_pgsql \
    && apk del .build-deps \
    && mkdir -p /run/nginx /var/log/supervisor \
    && rm -f /etc/nginx/http.d/default.conf

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .
COPY docker/nginx.conf.template /etc/nginx/http.d/default.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint"]