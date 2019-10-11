#!/bin/bash
composer update
php artisan key:generate
nohup php artisan queue:work --daemon --sleep=3 --tries=3 > /dev/null &
php artisan serve --host=0.0.0.0 --port=9000