#!/bin/bash
# update composer packages
composer update
# update node package manager
npm update
# generate new application key
php artisan key:generate
# update the loaded enviromental
php artisan config:clear