#!/bin/sh
composer install
npm install
php artisan migrate --seed

chmod -R 777 /var/www/storage  \
    && chmod -R 777 /var/www/bootstrap \
    && chown -R www-data:www-data /var/www/storage \
    && chown -R www-data:www-data /var/www/bootstrap
    
php-fpm &

npm run dev &

wait