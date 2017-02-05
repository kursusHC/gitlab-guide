#!/bin/bash

# Let's Encrypt - Auto register 

# In case there is no folder
shopt -s nullglob

# Get folder list 
FOLDERS=(/home/preview/projets/*)
for folder in "${FOLDERS[@]}"; do
    [[ -d "$folder" ]]
    foldername=$(basename $folder)
    domains="$domains -d $foldername.timmxware.fr"
done

# Add known domains 
fulllist="-d preview.timmxware.fr -d chat.timmxware.fr $domains"

# Register domains 
certbot certonly --expand --config-dir /home/preview/letsencrypt --logs-dir /home/preview/letsencrypt --work-dir /home/preview/letsencrypt -a webroot -w /home/preview/letsencrypt/www $fulllist
