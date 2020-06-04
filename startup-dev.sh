#!/bin/bash
# Install composer dependicies
composer install
# generate new application key
php artisan key:generate
# Start job queue(s) to process file imports
nohup php artisan queue:work --queue=importQueue, indexQueue --daemon --tries=3 > /dev/null &

# nohup php artisan queue:work --queue=importQueue --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=default --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &

# Start job queue to build the search index
# nohup php artisan queue:work --queue=indexQueue0 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue1 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue2 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue3 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue4 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue5 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue6 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue7 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue8 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# nohup php artisan queue:work --queue=indexQueue9 --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
# Clear generated config so phpunit can use css_testing as the database
php artisan config:clear
# Start web server
php artisan serve --host=0.0.0.0 --port=9000