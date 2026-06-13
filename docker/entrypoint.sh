#!/bin/bash
set -e

cd /var/www/html

export PORT="${PORT:-8080}"

mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/menu-cache bootstrap/cache public/img/menuImg
chown -R www-data:www-data storage bootstrap/cache public/img/menuImg
chmod -R 775 storage bootstrap/cache public/img/menuImg storage/app/menu-cache

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY belum di-set. Jalankan 'php artisan key:generate --show' lokal, lalu paste ke Render Environment."
    exit 1
fi

if [ -z "$APP_URL" ] || [ "$APP_URL" = "http://localhost" ]; then
    echo "WARNING: APP_URL belum diset — set ke URL Render POS (contoh: https://pos-warkop-kayu.onrender.com)."
fi

php artisan config:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

php-fpm -D
exec nginx -g 'daemon off;'
