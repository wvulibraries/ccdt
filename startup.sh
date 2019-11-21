#!/bin/bash
composer install
php artisan key:generate
nohup php artisan queue:work --daemon --sleep=3 --tries=3 > /dev/null &
php-fpm