#!/bin/sh
set -eu

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs

if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install --no-interaction --prefer-dist

if ! grep -q '^APP_KEY=.' .env; then
    php artisan key:generate --force
fi

if [ ! -f "$DB_DATABASE" ]; then
    touch "$DB_DATABASE"
fi
php artisan migrate --force
npm ci --no-audit --no-fund
npm run build

exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
