#!/bin/bash

#title           :bootstrap.sh
#description     :This script will install LAMP stack and Composer on centos 7.2
#author		       :Ajay Krishna Teja Kavuri
#date            :20161014
#version         :0.2
#==============================================================================

#Formal update for no reason
yum -y update

#Setup Yum messages
rpm -Uvh http://repo.mysql.com/mysql-community-release-el7-5.noarch.rpm
rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
yum -y update
echo -e "----Added RPM's----\n\n"

# Install apache
yum -y install httpd httpd-devel httpd-manual httpd-tools
echo -e "----Installed Apache----\n\n"

# Install MySQL
yum -y install mysql-connector-java mysql-connector-odbc mysql-devel mysql-lib mysql-server
echo -e "----Installed MySQL----\n\n"

# Install MySQL mods
yum -y install mod_auth_kerb mod_auth_mysql mod_authz_ldap mod_evasive mod_perl mod_security mod_ssl mod_wsgi
echo -e "----Installed Auth Plugins for MySQL----\n\n"

# Install PHP 7
yum -y install php70w php70w-bcmath php70w-cli php70w-common php70w-gd php70w-ldap php70w-mbstring php70w-mcrypt php70w-mysql php70w-odbc php70w-pdo php70w-pear php70w-pear-Benchmark php70w-pecl-apc php70w-pecl-imagick php70w-pecl-memcache php70w-soap php70w-xml php70w-xmlrpc
echo -e "----Installed PHP 7----\n\n"

# Start and set apache
sudo systemctl start httpd
sudo systemctl enable httpd
echo -e "----Started Apache----\n\n"

# Start and set MySQl
sudo systemctl start mysqld
sudo systemctl enable mysqld
echo -e "----Started MySQL----\n\n"

# Install composer
yum -y install nodejs
yum -y install npm
npm install --global gulp-cli
echo -e "----Installed Node and Gulp----\n\n"

# Install composer
curl -sS https://getcomposer.org/installer | php
sudo chmod +x composer.phar
mv composer.phar /usr/bin/composer
echo -e "----Installed composer----\n\n"
