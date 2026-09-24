#!/bin/sh

if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php artisan key:generate --show)
fi

php artisan migrate --force
php artisan config:cache
php artisan route:cache
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
