#!/bin/bash

#***************************************#
#                                       #
#         WELCOME TO YOUR DOOM          #
#                                       #
#***************************************#


# set timezone
sudo timedatectl set-timezone "$TIMEZONE"

# ssh to mounted folder
echo "cd /vagrant" >> /home/vagrant/.bashrc


#***************************************#
#                                       #
#             MAIN SERVER               #
#                                       #
#***************************************#


# update / upgrade
sudo apt-get update
#sudo apt-get -y upgrade

# install Apache
sudo apt-get -y install apache2

# enable mod_rewrite
sudo a2enmod proxy proxy_http ssl rewrite vhost_alias

# give access to error log
sudo chmod -R go+rX /var/log/apache2

# prepare web log viewer
sudo mkdir /var/www/apache

# Run Apache as Vagrant user for better permissions management
sudo sed -i 's/APACHE_RUN_USER=www-data/APACHE_RUN_USER=vagrant/' /etc/apache2/envvars
sudo sed -i 's/APACHE_RUN_GROUP=www-data/APACHE_RUN_GROUP=vagrant/' /etc/apache2/envvars
sudo grep -c 'APACHE_RUN_USER=www-data' /etc/apache2/envvars
sudo grep -c 'APACHE_RUN_GROUP=www-data' /etc/apache2/envvars
sudo chown -R vagrant:www-data /var/lock/apache2

# restart
sudo service apache2 restart

#***************************************#
#                                       #
#         ADDITIONAL PACKAGES           #
#                                       #
#***************************************#


# install other package
sudo apt-get -y install curl unzip


#***************************************#
#                                       #
#                PHP                    #
#                                       #
#***************************************#


# install PHP
if [ $PHPVERSION = "5" ]
then
sudo apt-get -y install php5 php-pear php5-mcrypt php5-gd php5-curl php-apc php5-mysql
else
echo "deb http://packages.dotdeb.org jessie all" | sudo tee /etc/apt/sources.list.d/dotdeb.list
wget -O- https://www.dotdeb.org/dotdeb.gpg | sudo apt-key add -
sudo apt-get update
sudo apt-get -y install php7.0 libapache2-mod-php7.0 php7.0-curl php7.0-mysql php7.0-json php7.0-gd php7.0-mcrypt php7.0-msgpack php7.0-memcached php7.0-intl php7.0-sqlite3 php7.0-gmp php7.0-geoip php7.0-mbstring php7.0-xml php7.0-zip php7.0-imagick
fi


#***************************************#
#                                       #
#              DEVTOOLS                 #
#                                       #
#***************************************#

# Nodejs latest
wget -qO- https://deb.nodesource.com/setup_6.x | sudo bash -
sudo apt-get update
sudo apt-get -y install nodejs

# nice tools
sudo apt-get -y install build-essential ruby-all-dev rubygems git subversion
sudo npm install -g less


#***************************************#
#                                       #
#               MYSQL                   #
#                                       #
#***************************************#


# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"
sudo apt-get -y install mysql-server

# create mysql database
mysql -u root -p"$PASSWORD" -e "CREATE DATABASE ${PROJECTNAME};"

# import selected database
mysql -u root -p"$PASSWORD" "$PROJECTNAME" < /vagrant/databases/$DATABASE


#***************************************#
#                                       #
#             PHPMYADMIN                #
#                                       #
#***************************************#


# install phpmyadmin
wget https://files.phpmyadmin.net/phpMyAdmin/$PMAVERSION/phpMyAdmin-$PMAVERSION-english.zip
sudo unzip phpMyAdmin-$PMAVERSION-english.zip -d /var/www/
sudo mv /var/www/phpMyAdmin-$PMAVERSION-english/ /var/www/phpmyadmin/
sudo rm -f phpMyAdmin-$PMAVERSION-english.zip

#Set up phpmyadmin user and tables for advanced features
mysql -u root -p"$PASSWORD" < /var/www/phpmyadmin/sql/create_tables.sql
mysql -u root -p"$PASSWORD" -e "GRANT SELECT, INSERT, DELETE, UPDATE ON phpmyadmin.* TO 'phpmyadmin'@'localhost' IDENTIFIED BY '$PASSWORD'"


#***************************************#
#                                       #
#             ADMINER                   #
#                                       #
#***************************************#


# install Adminer
sudo mkdir /var/www/adminer
sudo cp /vagrant/vagrant/server/adminer.php /var/www/adminer/adminer.php


#***************************************#
#                                       #
#             MAILCATCHER               #
#                                       #
#***************************************#


# install mailcatcher
sudo apt-get -y install sqlite3 libsqlite3-dev
sudo gem install --no-rdoc --no-ri mailcatcher

#***************************************#
#                                       #
#              COMPOSER                 #
#                                       #
#***************************************#


# install Composer
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer



#***************************************#
#                                       #
#              SYMFONY                  #
#                                       #
#***************************************#




