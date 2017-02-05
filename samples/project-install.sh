#!/bin/bash

# Install your app :
#composer --prefer-dist --no-dev install
# etc

# Give permission to server (no more chmod!)
# User www-data and group preview :
# setfacl -Rm user:www-data:rwx app/cache
# setfacl -Rm group:preview:rwx app/cache

