#!/bin/sh
set -eu
umask 0022
cd /var/www/html
mkdir -p storage/app/public storage/app/private storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
php docker/check-environment.php
# Per-container caches are built using runtime secrets, never image build secrets.
php artisan config:cache --quiet
php artisan route:cache --quiet
php artisan event:cache --quiet
exec "$@"
