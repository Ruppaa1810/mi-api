#!/bin/sh

if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php artisan key:generate --show)
fi

php artisan migrate --force

if [ -n "$ADMIN_PASSWORD" ]; then
  php artisan db:seed --class=AdminSeeder --force
fi

php artisan config:cache
php artisan route:cache
export PHP_CLI_SERVER_WORKERS=${PHP_CLI_SERVER_WORKERS:-4}
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
