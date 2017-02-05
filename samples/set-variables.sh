#!/bin/bash

# Usage (no '$' in variable name) :
# ./set-variables.sh file VAR1 VAR2 VAR3

# Usage for a Vagrant env (set your variables in vagrant/dev-variables.rb):
# ./set-variables.sh.sh dev file

ENV=$1

# Dev env (vagrant), get variables from vagrant/dev-variables.rb and replace them
if [ "$ENV" = "dev" ] ; then
		FILE=$2
		declare -A settings
		source ./vagrant/project-variables.rb
	if  [ "$settings[$PROJECT_PARAMS_FILE]" != "replaceMe" ] ; then
		for i in "${!settings[@]}" ; do
			SETTINGS="s/\${$i}/${settings[$i]}/"
			sed -i "$SETTINGS" "$FILE"
		done
	fi

# Every other envs (first arg is probably a filename), get variables from the Gitlab server
else
	FILE=$1
	shift
	for var in "${@}"; do
		SETTINGS="s/\${$var}/${!var}/"
		sed -i "$SETTINGS" "$FILE"
	done
fi