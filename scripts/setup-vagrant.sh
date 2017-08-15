#!/bin/bash

#title           :setup.sh
#description     :This script will set permissions for existing laravel on PHP 7
#author		       :Ajay Krishna Teja Kavuri
#date            :20170510
#version         :0.3
#==============================================================================

# Install composer packages
cd /var/www/html/project-css/
composer update

# Install Laravel Elixir
sudo npm install

# Setup the environment variables
cp .env.vagrant .env
php artisan key:generate

# Setup the right permissions
chmod 775 /var/www/html/project-css/storage
sudo chown -R apache:apache /var/www/html/project-css
sudo chmod 755 /var/www

# Some apache config for project
sudo ln -s /vagrant/conf/laravel.conf /etc/httpd/conf.d/
sudo systemctl restart httpd

# Set up MYSQL Stuff
mysql -u root < /vagrant/conf/setup.sql

# Run the migration
cd /var/www/html/project-css/
php artisan migrate --seed
