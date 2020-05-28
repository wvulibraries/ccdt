#!/bin/bash
# Install composer dependicies
composer install
# generate new application key
php artisan key:generate
# Start job queue to process file imports
nohup php artisan queue:work --queue=importQueue --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# Start job queue to build the search index
nohup php artisan queue:work --queue=indexQueue --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# Clear generated config so phpunit can use css_testing as the database
php artisan config:clear
# Start web server
php artisan serve --host=0.0.0.0 --port=9000