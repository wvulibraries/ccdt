#!/bin/bash

#title           :setup.sh
#description     :This script will set permissions for existing laravel on PHP 7
#author		       :Ajay Krishna Teja Kavuri
#date            :20161019
#version         :0.1
#==============================================================================

# Set the configurations
chmod 775 /var/www/html/project-css/storage
sudo chown -R apache:apache /var/www/html/project-css
sudo chmod 755 /var/www
sudo rm -R /etc/httpd/conf/httpd.conf
sudo ln -s /vagrant/serverConfiguration/httpd.conf /etc/httpd/conf
sudo systemctl restart httpd

# Set up MYSQL Stuff
mysql -u root < /vagrant/sqlFiles/setup.sql
