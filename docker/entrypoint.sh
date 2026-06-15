#!/bin/sh
set -e

cd /var/www/html

# Named storage volume may be empty on first run.
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Idempotent public/storage symlink for uploaded files.
if [ ! -e public/storage ]; then
    php artisan storage:link --no-interaction 2>/dev/null || ln -sfn ../storage/app/public public/storage
fi

# Warm caches only when runtime env is present (APP_KEY set). Skip during image build.
if [ -n "${APP_KEY}" ]; then
    php artisan config:cache --no-interaction 2>/dev/null || true
    php artisan route:cache --no-interaction 2>/dev/null || true
    php artisan view:cache --no-interaction 2>/dev/null || true
fi

exec docker-php-entrypoint "$@"
