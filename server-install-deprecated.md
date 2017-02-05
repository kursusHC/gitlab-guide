
##  Déprécié - Permission 

### /etc/sudoers

```
  GNU nano 2.2.6                                                         File: /etc/sudoers                                                                                                               Modified

#
# This file MUST be edited with the 'visudo' command as root.
#
# Please consider adding local content in /etc/sudoers.d/ instead of
# directly modifying this file.
#
# See the man page for details on how to write a sudoers file.
#
Defaults        env_reset
Defaults        mail_badpass
Defaults        secure_path="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

gitlab-runner ALL=(ALL) NOPASSWD: /bin/chown
```

Permet de lancer un

```
sudo /bin/chown -R user:group *
```

au build

## Déprécié -- Installation avec Apache au lieu de Ngnix

```ruby
external_url 'http://preview.timmxware.fr'
gitlab_workhorse['enable'] = true
gitlab_workhorse['listen_network'] = "tcp"
gitlab_workhorse['listen_addr'] = "127.0.0.1:8181"
web_server['external_users'] = ['www-data']
nginx['enable'] = false
```

```bash
install apache
a2enmod virtual_hosts proxy_ proxy_http ssl rewrite vhost_alias headers
```

```bash
<VirtualHost *:80>
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}$1 [R=301,L]
</VirtualHost>

<VirtualHost *:443>
  ServerName preview.timmxware.fr
  ProxyPreserveHost On
  ServerSignature Off
    RewriteEngine on
 DocumentRoot /opt/gitlab/embedded/service/gitlab-rails/public
  <Location />
    Order deny,allow
    Allow from all    Options FollowSymLinks
    AllowOverride All
    Require all granted
    ProxyPass http://127.0.0.1:8080/
    ProxyPassReverse http://127.0.0.1:8080
    ProxyPassReverse http://preview.timmxware.fr/
 </Location>
  RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_FILENAME} !-f
  RewriteRule .* http://127.0.0.1:8080%{REQUEST_URI} [P,QSA]
</VirtualHost>

<VirtualHost *:443>
  UseCanonicalName Off
  ServerName timmxware.fr
  ServerAlias *.timmxware.fr

  VirtualDocumentRoot /home/preview/%1/web

  <Directory ~ "/home/preview/*/public">
    Options Indexes FollowSymlinks MultiViews
    AllowOverride All
    Require all granted

  </Directory>
</VirtualHost>


<VirtualHost *:443>
  ServerName nectar.gitlab.local
  RewriteEngine on
 DocumentRoot /home/preview/nectar/public
  <Directory /home/preview/nectar/public>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
 </Directory>
</VirtualHost>
```

## Déprecié - Hooks au nom de gitlab
```bash
sudo -u git -H mkdir custom_hooks
sudo -u git -H vim post-receive
```

## Déprecié - Deploy script for server
```bash
git clone
git checkout master
gitlab hook
bootstrap.sh
restart.sh
rm -- "$0"
```


## Déprécié : Mattermost with Apache

```bash
a2enmod proxy_balancer
a2enmod headers
 a2enmod proxy_wstunnel


 <VirtualHost *:80>
  ServerName mattermost.gitlab.com

  ProxyPreserveHost On
  RewriteEngine     On

  RewriteCond %{REQUEST_URI}  ^/api/v1/websocket      [NC,OR]
  RewriteCond %{HTTP:UPGRADE} ^WebSocket$             [NC,OR]
  RewriteCond %{HTTP:CONNECTION} ^Upgrade$            [NC]
  RewriteRule .* ws://127.0.0.1:8065%{REQUEST_URI}   [P,QSA,L]

  RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_FILENAME}    !-f
  RewriteRule .* http://127.0.0.1:8065%{REQUEST_URI} [P,QSA,L]

  # Be sure to uncomment the next 2 lines if https is used
  # RequestHeader set X-Forwarded-Proto "https"
  # Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"


  # Prevent apache from sending incorrect 304 status updates
  RequestHeader unset If-Modified-Since
  RequestHeader unset If-None-Match

  <Location /api/v1/websocket>
     Require all granted
     ProxyPassReverse ws://127.0.0.1:8065/api/v1/websocket
     ProxyPassReverseCookieDomain 127.0.0.1 mattermost.gitlab.com
  </Location>

  <Location />
     Require all granted
     ProxyPassReverse http://127.0.0.1:8065/
     ProxyPassReverseCookieDomain 127.0.0.1 mattermost.gitlab.com
  </Location>
</VirtualHost>

*********** mattermost vhost HTTPS ******************************


<VirtualHost *:443>
ServerName mattermost.gitlab.com

DocumentRoot /usr/share/webapps/mattermost/web

SSLEngine on
SSLCertificateFile /etc/ssl/certs/apache-selfsigned.crt
SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key

ProxyPreserveHost On

RewriteEngine On

RewriteCond %
{REQUEST_URI} ^/api/v1/websocket [NC,OR]
RewriteCond %{HTTP:UPGRADE} ^WebSocket$ [NC,OR]
RewriteCond %{HTTP:CONNECTION} ^Upgrade$ [NC]
RewriteRule .* ws://127.0.0.1:8065%{REQUEST_URI}

[P,QSA,L]

RewriteCond %
{DOCUMENT_ROOT}

/%
{REQUEST_FILENAME}

!-f
RewriteRule .* http://127.0.0.1:8065%
{REQUEST_URI}

[P,QSA,L]
RequestHeader set X-Forwarded-Proto "https"

<Location /api/v1/websocket>
Require all granted
ProxyPassReverse ws://127.0.0.1:8065/api/vi/websocket
ProxyPassReverseCookieDomain 127.0.0.1 mattermost.gitlab.com
</Location>

<Location />
Require all granted
ProxyPassReverse http://127.0.0.1:8065/
ProxyPassReverseCookieDomain 127.0.0.1 mattermost.gitlab.com
</Location>

LogFormat "%
{X-Forwarded-For}

i %l %u %t \"%r\" %>s %b" common_forwarded
ErrorLog /var/log/httpd/mattermost.gitlab.com_error.log
CustomLog /var/log/httpd/mattermost.gitlab.com_forwarded.log common_forwarded
CustomLog /var/log/httpd/mattermost.gitlab.com_access.log combined env=!dontlog
CustomLog /var/log/httpd/mattermost.gitlab.com.log combined
</VirtualHost>
```



