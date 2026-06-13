FROM php:8.2-cli-bookworm AS vendor

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM php:8.2-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    gettext-base \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql pdo_mysql zip gd opcache \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php.ini /usr/local/etc/php/conf.d/99-render.ini
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

COPY --from=vendor /app /var/www/html

WORKDIR /var/www/html

RUN chmod +x /var/www/html/docker/fix-render-env.sh \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/menu-cache bootstrap/cache public/img/menuImg \
    && chown -R www-data:www-data storage bootstrap/cache public/img/menuImg \
    && chmod -R 775 storage bootstrap/cache public/img/menuImg storage/app/menu-cache

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
