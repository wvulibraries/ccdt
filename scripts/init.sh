#!/bin/bash

#title           :init.sh
#description     :This script will install laravel on PHP 7
#author		       :Ajay Krishna Teja Kavuri
#date            :20161014
#version         :0.1
#==============================================================================

# Remove if something already exists
rm -rf /var/www/html/project-css

# Change the directory
cd /home/vagrant

# Create a sample laravel project
# laravel new project-css
composer create-project --prefer-dist laravel/laravel project-css

# Install node dependencies and install d3 gulp
cd ./project-css
npm install

# Move into Apache
sudo mv /home/vagrant/project-css /var/www/html

# Set the configurations
chmod 775 /var/www/html/project-css/storage
sudo chown -R apache:apache /var/www/html/project-css
sudo chmod 755 /var/www
sudo rm -R /etc/httpd/conf/httpd.conf
sudo ln -s /vagrant/serverConfiguration/httpd.conf /etc/httpd/conf
sudo systemctl restart httpd

#Set up MYSQL Stuff
mysql -u root < /vagrant/sqlFiles/setup.sql
