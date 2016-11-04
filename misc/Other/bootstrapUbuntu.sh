#!/bin/bash

#title           :bootstrapUbuntu.sh
#description     :This script will install PHP stack and Composer on Ubuntu for Laravel
#author		       :Ajay Krishna Teja Kavuri
#date            :20161014
#version         :0.1
#==============================================================================

# Formal update for no reason
sudo apt-get -y update
echo -e "---- Updated ----\n\n"

# Install PHP
sudo apt-get install -y libapache2-mod-php7.0 php7.0-fpm php7.0-common php7.0-cli php-pear php7.0-curl php7.0-gd php7.0-gmp php7.0-intl php7.0-imap php7.0-json php7.0-ldap php7.0-mbstring php7.0-mcrypt php7.0-mysql php7.0-ps php7.0-readline php7.0-tidy php7.0-xmlrpc php7.0-xsl
echo -e "---- Installed PHP boy/girl!! ----\n\n"

# Install Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
echo -e "---- Installed Composer boy/girl!! ----\n\n"

# Install node
curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -
sudo apt-get install -y nodejs
