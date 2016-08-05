#!/bin/bash

#title           :bootstrap.sh
#description     :This script will install Apache, MySQL, PHP, Git, Engine PHP 4.0 on centos 6.4
#author		       :Ajay Krishna Teja Kavuri
#date            :20160803
#version         :0.1
#==============================================================================

## Just update it, never hurts
echo "Updating system"
yum -y update

## Install Apache
echo "Installing Apache..."
yum -y install httpd httpd-devel httpd-manual httpd-tools

## Install MySQL
echo "Installing MySQL..."
yum -y install mysql-connector-java mysql-connector-odbc mysql-devel mysql-lib mysql-server
yum -y install mod_auth_kerb mod_auth_mysql mod_authz_ldap mod_evasive mod_perl mod_security mod_ssl mod_wsgi

## Install PHP
echo "Installing PHP..."
yum -y install php php-bcmath php-cli php-common php-gd php-ldap php-mbstring php-mcrypt php-mysql php-odbc php-pdo php-pear php-pear-Benchmark php-pecl-apc php-pecl-imagick php-pecl-memcache php-soap php-xml php-xmlrpc

## Install Emacs and Git
echo "Installing Emacs and Git..."
yum -y install emacs emacs-common emacs-nox
yum -y install git

## Some configuration for apache
echo "Configuring apache..."
mv /etc/httpd/conf.d/mod_security.conf /etc/httpd/conf.d/mod_security.conf.bak
/etc/init.d/httpd start
chkconfig httpd on

## Setting some variables for use
echo "Echo setting variables..."
GITDIR="/tmp/git"
ENGINEAPIGIT="https://github.com/wvulibraries/engineAPI.git"
ENGINEBRANCH="master"

SERVERURL="/home/www.libraries.wvu.edu"
DOCUMENTROOT="public_html"
SITEROOT=$DOCUMENTROOT/rockefeller-css


## Load the engine
echo "Loading Engine API..."
mkdir -p $GITDIR
cd $GITDIR
git clone -b $ENGINEBRANCH $ENGINEAPIGIT
git clone https://github.com/wvulibraries/engineAPITemplates.git
git clone https://github.com/wvulibraries/engineAPI-Modules.git

mkdir -p $SERVERURL/phpincludes/
ln -s $GITDIR/engineAPITemplates/* $GITDIR/engineAPI/engine/template/
ln -s $GITDIR/engineAPI-Modules/src/modules/* $GITDIR/engineAPI/engine/engineAPI/latest/modules/
ln -s $GITDIR/engineAPI/engine/ $SERVERURL/phpincludes/

rm -f $GITDIR/engineAPI/engine/engineAPI/latest/config/defaultPrivate.php
ln -s /vagrant/serverConfiguration/defaultPrivate.php $GITDIR/engineAPI/engine/engineAPI/latest/config/defaultPrivate.php

## Load and link application stuff
echo "Setting application configuration"
mkdir -p $SERVERURL/$DOCUMENTROOT/admin

ln -s /vagrant/src/ $SERVERURL/$SITEROOT
ln -s $SERVERURL/phpincludes/engine/engineAPI/latest $SERVERURL/phpincludes/engine/engineAPI/4.0

rm -f /etc/php.ini
rm -f /etc/httpd/conf/httpd.conf

ln -s /vagrant/serverConfiguration/php.ini /etc/php.ini
ln -s /vagrant/serverConfiguration/vagrant_httpd.conf /etc/httpd/conf/httpd.conf
mkdir -p /vagrant/serverConfiguration/serverlogs
touch /vagrant/serverConfiguration/serverlogs/error_log
/etc/init.d/httpd restart

mkdir -p $SERVERURL/phpincludes/databaseConnectors/
ln -s /vagrant/serverConfiguration/database.lib.wvu.edu.remote.php $SERVERURL/phpincludes/databaseConnectors/database.lib.wvu.edu.remote.php

### Template
mkdir -p $GITDIR/engineAPITemplates/library2012.2col/templateIncludes
ln -s /vagrant/serverConfiguration/templateHeader.php $GITDIR/engineAPITemplates/library2012.2col/templateIncludes/templateHeader.php
ln -s /vagrant/serverConfiguration/templateFooter.php $GITDIR/engineAPITemplates/library2012.2col/templateIncludes/templateFooter.php
ln -s $GITDIR/engineAPITemplates/library2012.1col/templateIncludes/2colHeaderIncludes.php $GITDIR/engineAPITemplates/library2012.2col/templateIncludes/2colHeaderIncludes.php

mkdir -p $GITDIR/engineAPITemplates/library2012.3col/templateIncludes
ln -s /vagrant/serverConfiguration/templateHeader.php $GITDIR/engineAPITemplates/library2012.3col/templateIncludes/templateHeader.php
ln -s /vagrant/serverConfiguration/templateFooter.php $GITDIR/engineAPITemplates/library2012.3col/templateIncludes/templateFooter.php
ln -s $GITDIR/engineAPITemplates/library2012.1col/templateIncludes/3colHeaderIncludes.php $GITDIR/engineAPITemplates/library2012.3col/templateIncludes/3colHeaderIncludes.php

### Create Favicon
touch /home/www.libraries.wvu.edu/public_html/favicon.ico

### Base Post Setup
ln -s $SERVERURL $ENGINEAPIHOME
ln -s $GITDIR/engineAPI/public_html/engineIncludes/ $SERVERURL/$DOCUMENTROOT/engineIncludes

## Setup the EngineAPI Database
/etc/init.d/mysqld start
chkconfig mysqld on
mysql -u root < /tmp/git/engineAPI/sql/vagrantSetup.sql
mysql -u root EngineAPI < /tmp/git/engineAPI/sql/EngineAPI.sql

## Create a simple MySQL base database
mysql -u root < /vagrant/sqlFiles/setup.sql
mysql -u root rockefellercss < /vagrant/sqlFiles/base.sql
