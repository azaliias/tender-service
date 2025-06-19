#!/bin/sh

set -e

cd /var/www/html

# Ожидаем доступности MySQL
until php artisan db:monitor > /dev/null 2>&1; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 1
done

# Выполняем миграции и сиды
php artisan migrate --force
php artisan db:seed --force

# Запускаем основной процесс
exec "$@"