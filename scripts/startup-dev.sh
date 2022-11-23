#!/bin/bash
# remove existing env file if one exists
rm -f /var/www/.env

# removing testing env file if one exists
rm -f /var/www/.env.testing

# link env file
ln -s /env/.env.dev /var/www/.env

# link currnent testing env file
ln -s /env/.env.testing /var/www/.env.testing

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

# Clear generated config so phpunit can use css_testing as the database
php artisan config:clear

# Start web server
php artisan serve --host=0.0.0.0 --port=9000