#!/bin/bash
# link env file
ln -s /env/.env.prod /var/www/.env

# Install composer dependicies
composer install
# generate new application key
php artisan key:generate
# Start job queue(s) to process file imports

# single job worker for 3 queues
#nohup php artisan queue:work --queue=high,default,low --daemon --sleep=3 --tries=3 > /dev/null &

# multiple job workers
nohup php artisan queue:work database --queue=high,low --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
nohup php artisan queue:work database --queue=low --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
nohup php artisan queue:work database --queue=low --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &
nohup php artisan queue:work database --queue=default,low --daemon --sleep=3 --timeout=86400 --tries=3 > /dev/null &

php-fpm