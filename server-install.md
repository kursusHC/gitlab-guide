# Installation du serveur

## Installation de Gitlab Omnibus

https://about.gitlab.com/downloads/

## Pre config avec URL non https 

nano /etc/gitlab/gitlab.rb

```ruby
external_url 'http://preview.timmxware.fr'
gitlab-ctl reconfigure 
```

## Web user creation and setup
```bash
adduser preview
su preview -c "mkdir /home/preview/projets"
su preview -c "mkdir /home/preview/ci"
```




## Nginx config pour autres vhosts

### /etc/nginx/main.conf

```bash
server {
listen 443;
server_name ~^(?<sname>.+?).timmxware.fr$;
root /home/preview/projets/$sname/web;

index index.html index.htm index.php;

charset utf-8;

#location / {
#index index.php; # Not sure if needed
#try_files /$uri /$uri/ /index.php?url=$uri&$args;
#}

        location / {
        try_files $uri $uri/ /index.php;
        }

## Let's encypt
location ^~ /.well-known {
alias /home/preview/letsencrypt/www/.well-known;
}

## PHP7
#location ~ \.php$ {
#include /etc/nginx/fastcgi.conf;
#fastcgi_pass unix:/run/php/php7.0-fpm.sock;
#}

location ~ \.php$ {
   proxy_set_header X-Real-IP  $remote_addr;
   proxy_set_header X-Forwarded-For $remote_addr;
   proxy_set_header Host $host;
   proxy_pass http://127.0.0.1:8080;
}

## Exclude htaccess
location ~ /\.ht {
deny all;
}



}
```

## Gitlab Configuration

nano /etc/gitlab/gitlab.rb

```ruby
external_url 'https://preview.timmxware.fr'
nginx['redirect_http_to_https'] = true
nginx['ssl_certificate'] = "/etc/letsencrypt/live/preview.timmxware.fr/fullchain.pem"
nginx['ssl_certificate_key'] = "/etc/letsencrypt/live/preview.timmxware.fr/privkey.pem"
nginx['custom_gitlab_server_config'] = "location ^~ /.well-known {\n alias /home/preview/letsencrypt/www/.well-known;\n}\n"
nginx['custom_nginx_config'] = "include /etc/nginx/main.conf;"

mattermost_nginx['redirect_http_to_https'] = true
mattermost_nginx['ssl_certificate']= "/etc/letsencrypt/live/gitlab.timmxware.fr/fullchain.pem"
mattermost_nginx['ssl_certificate_key'] = "/etc/letsencrypt/live/preview.timmxware.fr/privkey.pem"
mattermost_nginx['custom_gitlab_mattermost_server_config']="location ^~ /.well-known {\n alias /home/preview/letsencrypt/www/.well-known;\n}\n"
```

```
gitlab-ctl reconfigure
```



### /etc/nginx/fastcgi.conf :

```bash
fastcgi_param  QUERY_STRING       $query_string;
fastcgi_param  REQUEST_METHOD     $request_method;
fastcgi_param  CONTENT_TYPE       $content_type;
fastcgi_param  CONTENT_LENGTH     $content_length;

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
fastcgi_param  REQUEST_URI        $request_uri;
fastcgi_param  DOCUMENT_URI       $document_uri;
fastcgi_param  DOCUMENT_ROOT      $document_root;
fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
fastcgi_param  SERVER_PROTOCOL    $server_protocol;
fastcgi_param  PATH_INFO          $fastcgi_script_name;
fastcgi_param  HTTPS              $https if_not_empty;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

fastcgi_param  REMOTE_ADDR        $remote_addr;
fastcgi_param  REMOTE_PORT        $remote_port;
fastcgi_param  SERVER_ADDR        $server_addr;
fastcgi_param  SERVER_PORT        $server_port;
fastcgi_param  SERVER_NAME        $server_name;
```

## Let's encrypt authorisations

### Instal certbot

```
# Install debian backports before
apt-get install certbot -t jessie-backports
```


### Let certbot run as non root

```
su preview -c "mkdir /home/preview/letsencrypt"
# This folder is used for LE logs and stuff, it is set in auto-letsencrypt.sh
su preview -c "mkdir /home/preview/letsencrypt/www"
# This folder is used for LE verification
```

puis lancer le certbot une fois pour set up la config LE

```
certbot certonly --expand -a webroot -w /var/www/letsencrypt -d preview.timmxware.fr -d projector.timmxware.fr -d chat.timmxware.fr
```

## Install Apache en backend 8080 

apt-get install apache2

### Command for all domains

cf auto-letsencrypt.sh

```
certbot certonly --expand -a webroot -w /var/www/letsencrypt -d preview.timmxware.fr -d projector.timmxware.fr -d chat.timmxware.fr
```
### Command for one domain
```
certbot certonly -a webroot -w /home/preview/projets/temporator/web -d temporator.timmxware.fr
```

## Gitlab runner

### Installation
```
curl -L https://packages.gitlab.com/install/repositories/runner/gitlab-ci-multi-runner/script.deb.sh | sudo bash
sudo apt-get install gitlab-ci-multi-runner

sudo gitlab-runner uninstall
gitlab-runner install --working-directory /home/preview/ci --user preview
service gitlab-runner restart

sudo gitlab-ci-multi-runner register
```



## Gitlab CI - Symfony

```ruby
# Composer stores all downloaded packages in the vendor/ directory.
# Do not use the following if the vendor/ directory is commited to
# your git repository.
cache:
paths:
- vendor/

before_script:
# Install composer dependencies
- curl --silent --show-error https://getcomposer.org/installer | php
- php composer.phar install
```


## Mattermost

### /etc/gitlab/gitlab.rb

```bash
mattermost_external_url 'http://mattermost.example.com'
```


## Let'sEcrypt + Gitlab


> The by far best solution I was able to find for now is described in this blog post. I won't recite everything, but the key points are:

> Use the webroot authenticator for Let's Encrypt

> Create the folder /var/www/letsencrypt and use this directory as webroot-path for Let's Encrypt

> Change the following config values in /etc/gitlab/gitlab.rb and run gitlab-ctl reconfigure after that:

```ruby
nginx['redirect_http_to_https'] = true
nginx['ssl_certificate']= "/etc/letsencrypt/live/preview.timmxware.fr/fullchain.pem"
nginx['ssl_certificate_key'] = "/etc/letsencrypt/live/preview.timmxware.fr/privkey.pem"
nginx['custom_gitlab_server_config']="location ^~ /.well-known {\n alias /home/preview/letsencrypt/www/.well-known;\n}\n"
```

## Let'sEcrypt + Gitlab + Mattermost

>If you are using Mattermost which is shipped with the Omnibus package then you can additionally set these options in /etc/gitlab/gitlab.rb:

```ruby
mattermost_nginx['redirect_http_to_https'] = true
mattermost_nginx['ssl_certificate']= "/etc/letsencrypt/live/gitlab.timmxware.fr/fullchain.pem"
mattermost_nginx['ssl_certificate_key'] = "/etc/letsencrypt/live/preview.timmxware.fr/privkey.pem"
mattermost_nginx['custom_gitlab_mattermost_server_config']="location ^~ /.well-known {\n alias /home/preview/letsencrypt/www/.well-known;\n}\n"
```
> After requesting your first certificate remember to change the external_url to https://... and again run gitlab-ctl reconfigure

> This method is very elegant since it just mounts the directory /var/www/letsencrypt/.well-known used by the Let's Encrypt authenticator into the Gitlab web-root via a custom Nginx configuration and authentication is always possible when Gitlab is running. This means that you can automatically renew the Let's Encrypt certificates.

## Vagrant - performance
https://stefanwrobel.com/how-to-make-vagrant-performance-not-suck


## Vagrant - self signed SSL
```
sudo openssl req -x509 -nodes -days 1972 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt
a2enmod ssl
a2ensite default-ssl
#change file called in default-ssl with generated key above
```

## Non utilisé : Deploy key for gitlab-runner user
```bash
`su gitlab-runner `
`ssh-keygen -t rsa -C "GitLab" -b 4096`
```


## A voir plus tard : Nginx + Apache

https://www.digitalocean.com/community/tutorials/how-to-configure-nginx-as-a-reverse-proxy-for-apache


## PHP
[MVC 1](http://requiremind.com/a-most-simple-php-mvc-beginners-tutorial/), [MVC2](https://www.dev-metal.com/mini-extremely-simple-barebone-php-application/)

