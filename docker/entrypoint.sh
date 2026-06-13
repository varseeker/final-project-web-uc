#!/bin/bash
set -e

cd /var/www/html

export PORT="${PORT:-8080}"

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/img/menuImg
chown -R www-data:www-data storage bootstrap/cache public/img/menuImg
chmod -R 775 storage bootstrap/cache public/img/menuImg

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY belum di-set. Jalankan 'php artisan key:generate --show' lokal, lalu paste ke Render Environment."
    exit 1
fi

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

php-fpm -D
exec nginx -g 'daemon off;'
