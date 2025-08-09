#!/bin/sh
composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
