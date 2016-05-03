[![Build Status](https://travis-ci.org/eugene-matvejev/battleship-game-api.svg?branch=master)](https://travis-ci.org/eugene-matvejev/battleship-game-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/?branch=master)
[![Code Climate](https://codeclimate.com/github/eugene-matvejev/battleship-game-api/badges/gpa.svg)](https://codeclimate.com/github/eugene-matvejev/battleship-game-api)
[![Code Coverage](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc/mini.png)](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc)


# Battleship Game API
##### THIS IS SPARE TIME PROJECT, WORK IN PROGRESS! HIGHLY EXPERIMENTAL!!!
#### Project purpose:
 * try 'cutting edge' technologies and approaches such as PHP7, SF3, ECMA6
 * simulate database loading e.g. upto 500 insertions/request
 * deliver preview about my technical knowledge before the job interview

### game cheat-code:
 * CPU have only one-cell ship which is hardcoded at __B2__ cell
  * if you will hit __B2__ cell - you will instantly win
   * purpose: to easier manual testing (as the project is far from 'finished' stage, as I keep trying polish it)

### future plans:
 * deliver back-end as OpenAPI using SF3, PHP7, Doctrine2 with various databases
  * try to create it later as well on Silex
 * separate front-end into separate repository and rewrite it using AngularJS 2
  * front-end already is done as single-page-application (SPA)
 * make simple and flexible database support e.g. MariaDB, MySQL, MongoDB
 * implement behat tests, consider kahlan as well

# software requirements:
 * supported databases:
  * MySQL => 5.5
  * MariaDB >= 9.*
 * WIP:
  * MongoDB
 * http server: apache/nginx with php >= 7.0.1
 * composer

### key technologies:
 * PHP7 (7.0.1, as 7.0.0 had bugged primitive types)
 * Symfony Framework 3 (SF3)
 * Doctrine 2
 * Doctrine Fixtures
 * PHPUnit 5
 * Behat 3
 * Composer
 * JMS Serializer
 * Twig
 * EMCAScript6 (JavaScript ES6)
 * CSS3
 * Twitter Bootstrap 3

# How to install
 * copy *app/config/parameters.yml.dist* to *app/config/parameters.yml* and amend database settings
 * *composer install* __# composer is configured to create databases if they not exists and run apply migrations__
 * __optional!__ *composer dump-autoload --optimize* __# to generate "hash-map" autoloader__
  * __NOTE!__ production uses __APC autoloader__
 * __optional!__ *php bin/console assets:install* __# to dump assets as hard copies__
  * __NOTE!__ by default assets are installed as symlinks
 * __optional!__ *php bin/console doctrine:fixtures:load --env=test* __# for testing purposes only__
 * apache virtual host config:
 ```
<VirtualHost 127.0.0.1:80 ::1:80>
    DocumentRoot "%PROJECT_ROOT_DIRECTORY%/web"
    ErrorLog "%PROJECT_ROOT_DIRECTORY%/var/logs/apache_log"

    ServerName api.game.local
    ServerAlias api.game.local
    <Directory %PROJECT_ROOT_DIRECTORY%/web>
        AllowOverride All
        Order Allow,Deny
        Allow from All

        Require all granted

        # Use the front controller as index file. It serves as a fallback solution when
        # every other rewrite/redirect fails (e.g. in an aliased environment without
        # mod_rewrite). Additionally, this reduces the matching process for the
        # start page (path "/") because otherwise Apache will apply the rewriting rules
        # to each configured DirectoryIndex file (e.g. index.php, index.html, index.pl).
        DirectoryIndex app.php

        # By default, Apache does not evaluate symbolic links if you did not enable this
        # feature in your server configuration. Uncomment the following line if you
        # install assets as symlinks or if you experience problems related to symlinks
        # when compiling LESS/Sass/CoffeScript assets.
        # Options FollowSymlinks

        # Disabling MultiViews prevents unwanted negotiation, e.g. "/app" should not resolve
        # to the front controller "/app.php" but be rewritten to "/app.php/app".
        <IfModule mod_negotiation.c>
            Options -MultiViews
        </IfModule>

        <IfModule mod_rewrite.c>
            RewriteEngine On

            # Determine the RewriteBase automatically and set it as environment variable.
            # If you are using Apache aliases to do mass virtual hosting or installed the
            # project in a subdirectory, the base path will be prepended to allow proper
            # resolution of the app.php file and to redirect to the correct URI. It will
            # work in environments without path prefix as well, providing a safe, one-size
            # fits all solution. But as you do not need it in this case, you can comment
            # the following 2 lines to eliminate the overhead.
            RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
            RewriteRule ^(.*) - [E=BASE:%1]

            # Sets the HTTP_AUTHORIZATION header removed by Apache
            RewriteCond %{HTTP:Authorization} .
            RewriteRule ^ - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

            # Redirect to URI without front controller to prevent duplicate content
            # (with and without `/app.php`). Only do this redirect on the initial
            # rewrite by Apache and not on subsequent cycles. Otherwise we would get an
            # endless redirect loop (request -> rewrite to front controller ->
            # redirect -> request -> ...).
            # So in case you get a "too many redirects" error or you always get redirected
            # to the start page because your Apache does not expose the REDIRECT_STATUS
            # environment variable, you have 2 choices:
            # - disable this feature by commenting the following 2 lines or
            # - use Apache >= 2.3.9 and replace all L flags by END flags and remove the
            #   following RewriteCond (best solution)
            RewriteCond %{ENV:REDIRECT_STATUS} ^$
            RewriteRule ^app\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]

            # If the requested filename exists, simply serve it.
            # We only want to let Apache serve files and not directories.
            RewriteCond %{REQUEST_FILENAME} -f
            RewriteRule ^ - [L]

            # Rewrite all other queries to the front controller.
            RewriteRule ^ %{ENV:BASE}/app.php [L]
        </IfModule>

        <IfModule !mod_rewrite.c>
            <IfModule mod_alias.c>
                # When mod_rewrite is not available, we instruct a temporary redirect of
                # the start page to the front controller explicitly so that the website
                # and the generated links can still be used.
                RedirectMatch 302 ^/$ /app.php/
                # RedirectTemp cannot be used instead
            </IfModule>
        </IfModule>
    </Directory>
</VirtualHost>
 ```

### How to execute tests
 * *phpunit -c test* or *php bin/phpunit -c tests* (fixtures will wipe and populate database before execute tests)

### used patterns
 * Front Controller
 * MVC
 * ORM
 * Data Mapper
 * Builder
 * Strategy
 * Factory
 * Singleton
 * Delegation
 * Registry
 * Service Locator
 * Event Dispatcher
 * Dependency Injection

### github usage:
 * semantic versioning with pull-requests into the master branch

### used standarts:
 * PHP-FIG:
  * ../PSR-2
  * ../PSR-4
