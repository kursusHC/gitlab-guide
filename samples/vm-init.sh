#!/bin/bash

##@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@##
##                                    ##
##      ADVANCED CONFIGURATION :      ##
##     DO NOT CHANGE THE FOLLOWING    ##
##        UNLESS YOU KNOW WHY         ##
##                                    ##
##     PLEASE DEFINE THE VARIABLES    ##
##         IN THE VAGRANTFILE         ##
##         BEFORE VAGRANT UP!         ##
##                                    ##
##@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@##

# Prevents Vagrant warning messages
export DEBIAN_FRONTEND=noninteractive

#***************************************#
#                                       #
#          VAGRANT VARIABLES            #
#                                       #
#***************************************#

# Get project settings
source /vagrant/vagrant/vm-settings.rb

# Export variables to replace them in config files
export PROJECTNAME PASSWORD IPADRESS DATABASE PHPVERSION PMAVERSION TIMEZONE

#***************************************#
#                                       #
#             BOOT NOW !                #
#                                       #
#***************************************#

ISFIRSTRUN=$1

if [ "$ISFIRSTRUN" = "true" ] ; then

# Is executed on first boot
install_script="/vagrant/vagrant/vm/vm-install.sh"
chmod +x "$install_script" && "$install_script" && chmod -x "$install_script"

else

# Is executed every boot
config_script="/vagrant/vagrant/vm/vm-configure.sh"
chmod +x "$config_script" && "$config_script" && chmod -x "$config_script"

fi