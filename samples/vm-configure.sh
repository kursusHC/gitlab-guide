#!/bin/bash

CONFIGVARS='$PROJECTNAME:$PASSWORD:$IPADRESS:$DATABASE:$PHPVERSION:$PMAVERSION:$TIMEZONE'

#***************************************#
#                                       #
#         APPLY ALL THE CONFIG          #
#                                       #
#***************************************#


# configure virtualhost (see server/virtualhost.conf)
envsubst "$CONFIGVARS" < /vagrant/vagrant/server/virtualhost.conf | sudo tee /etc/apache2/sites-available/000-default.conf

# configure Apache web logs (see server/apache-web-log.php)
envsubst "$CONFIGVARS" < /vagrant/vagrant/server/apache-web-log.php | sudo tee /var/www/apache/index.php

# Configure PHP (see server/php.ini)
if [ $PHPVERSION = "5" ] ; then PHPPATH="php" ; else PHPPATH="php/" ; fi
envsubst "$CONFIGVARS" < /vagrant/vagrant/server/php.ini | sudo tee /etc/$PHPPATH$PHPVERSION/apache2/conf.d/01-$PROJECTNAME.ini

# configure PhpMyAdmin (see server/phpmyadmin.php)
envsubst "$CONFIGVARS" < /vagrant/vagrant/server/phpmyadmin.php | sudo tee /var/www/phpmyadmin/config.inc.php

# start mailcatcher
mailcatcher --ip=0.0.0.0

# restart Apache
sudo service apache2 restart