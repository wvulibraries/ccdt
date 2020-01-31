#!/bin/bash
# Install composer dependicies
composer install
# generate new application key
php artisan key:generate
# Start job queue to process file imports
nohup php artisan queue:work --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# Clear generated config so phpunit can use css_testing as the database
php artisan config:clear
# Start web server
php artisan serve --host=0.0.0.0 --port=9000