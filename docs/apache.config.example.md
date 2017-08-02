## apache virtual host example
### /etc/hosts
```
127.0.0.1       api.game.local
::1             api.game.local
```

### apache virtual host config:
```
<VirtualHost 127.0.0.1:80 ::1:80>
    DocumentRoot "%PROJECT_ROOT_DIRECTORY%/web"
    ErrorLog "%PROJECT_ROOT_DIRECTORY%/var/logs/apache_log"

    ServerName api.game.local
    ServerAlias api.game.local

    <Directory "%PROJECT_ROOT_DIRECTORY%/web">
        AllowOverride All
        Order Allow,Deny
        Allow from All

        Require all granted

        DirectoryIndex app.php

        <IfModule mod_rewrite.c>
            RewriteEngine On

            RewriteCond %{REQUEST_METHOD} OPTIONS
            RewriteRule ^(.*)$ $1 [R=200,L]
            Header always set Access-Control-Allow-Origin "*"
            Header always set Access-Control-Allow-Methods "POST, GET, PATCH"

            RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
            RewriteRule ^(.*) - [E=BASE:%1]

            RewriteCond %{ENV:REDIRECT_STATUS} ^$
            RewriteRule ^app\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]

            RewriteCond %{REQUEST_FILENAME} -f
            RewriteRule ^ - [L]
            RewriteRule ^ %{ENV:BASE}/app.php [L]
        </IfModule>
    </Directory>
</VirtualHost>
```
