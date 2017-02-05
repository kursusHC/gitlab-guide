#!/bin/bash
pwd
composer --prefer-dist --no-dev install
setfacl -Rm user:www-data:rwx app/cache
setfacl -Rm group:preview:rwx app/cache
