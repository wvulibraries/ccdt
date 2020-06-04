#!/bin/bash
#cp .env.prod .env
composer install
php artisan key:generate
nohup php artisan queue:work --queue=importQueue --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
nohup php artisan queue:work --queue=indexQueue1 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
nohup php artisan queue:work --queue=indexQueue2 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
php-fpm