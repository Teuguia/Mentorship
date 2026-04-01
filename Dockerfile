FROM composer:2 AS vendor
WORKDIR /app

COPY . .
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

FROM node:22-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources resources
COPY public public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php:8.3-cli-alpine AS runtime
WORKDIR /var/www/html

RUN apk add --no-cache \
        bash \
        icu-data-full \
        icu-dev \
        libpq-dev \
        oniguruma-dev \
        postgresql-libs \
        unzip \
        zip \
    && docker-php-ext-install \
        bcmath \
        intl \
        pcntl \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        sockets

COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build
COPY docker/start-web.sh /usr/local/bin/start-web
COPY docker/start-reverb.sh /usr/local/bin/start-reverb

RUN chmod +x /usr/local/bin/start-web /usr/local/bin/start-reverb \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

EXPOSE 8000 8080

CMD ["start-web"]
