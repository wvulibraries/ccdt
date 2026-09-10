#!/bin/bash
# setup main database
cd /var/www
php artisan migrate --seed --database=mysql