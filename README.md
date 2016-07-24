[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/?branch=master)
[![Code Climate](https://codeclimate.com/github/eugene-matvejev/battleship-game-api/badges/gpa.svg)](https://codeclimate.com/github/eugene-matvejev/battleship-game-api)
[![Build Status](https://travis-ci.org/eugene-matvejev/battleship-game-api.svg?branch=master)](https://travis-ci.org/eugene-matvejev/battleship-game-api)
[![Build Status](https://codeship.com/projects/e893a4f0-0b28-0134-b0ad-129a07c0a376/status?branch=master)](https://codeship.com/projects/155781)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc/mini.png)](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc)

__TDD tests__
 * [![Circle CI](https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master.svg?style=svg)](https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master) - _PHPUnit_

__BDD tests__
 * [![Circle CI](https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master.svg?style=svg)](https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master) - _Behat_
 * [![Circle CI](https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master.svg?style=svg)](https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master) - _Kahlan_

__Test Coverage__

[![Code Coverage](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/?branch=master)
[![codecov](https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/master/graph/badge.svg)](https://codecov.io/gh/eugene-matvejev/battleship-game-api)

__DEMO__ : https://battleship-game-api.herokuapp.com/ (API)

# Battleship Game API
##### THIS IS SPARE TIME PROJECT, WORK IN PROGRESS! HIGHLY EXPERIMENTAL!!!
#### project purpose:
 * try out:
  * _cutting edge_ technologies such as PHP7, SF3, Doctrine2
  * _modern_ approaches such as Test Automation, Continuous Integration|Deployment
 * simulate database loading [~500 transactions per request]
 * deliver preview about my technical knowledge before the job interview

#### game cheat-code:
* AI players have only one ship[single-cell] which is located at __B2__ cell [_purpose: easier manual testing_]
 * if you will hit __B2__ cell - you will win

# software requirements
 * supported database engines:
  * MySQL >= 5.5
  * MariaDB >= 9.0
  * PostgreSQL >= 9.3
  * SQLite >= 3
 * WIP:
  * MongoDB
 * http server: apache/nginx with PHP7
 * Composer >= 1.0.3

# technology stack
### key technologies:
 * PHP7 (7.0.0 - 7.0.4 || >= 7.0.6 [7.0.5 had bugged SPL])
 * [Symfony Framework 3](http://symfony.com) [SF3]
 * [Doctrine 2](http://doctrine-orm.readthedocs.io/en/latest) with [Fixtures](http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html)
 * [PHPUnit 5](https://phpunit.de)
 * [Behat 3](http://docs.behat.org/en/v3.0)
 * [Kahlan](http://kahlan.readthedocs.io/en/latest)
 * [Composer](https://getcomposer.org)
 * [JMS Serializer](http://jmsyst.com/bundles/JMSSerializerBundle)
 * [API Doc](https://packagist.org/packages/nelmio/api-doc-bundle)
 * [Twig](http://twig.sensiolabs.org/)

### used patterns:
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

### PHP-FIG:
 * PSR-2
 * PSR-4
 * PSR-6

# workflow
 * new functionality merged into master branch only via pull requests
 * each pull request have callback to trigger CI engines such as Travis, Circle, CodeShip, Scrutinizer, Sensiolabs Insight, CodeCov
 * result of each pull request is ready-to-use release - using Continuous Delivery principles

#gitflow
  * __master__ branch: stable source code, contains release-ready source
  * __heroku__ branch: reflects current deployed app at heroku [Continuous Deployment]
  * __prototype_*__ branch: contains new idea [pull request of prototype branch is always next _major_ version release]
  * pull requests follows [semantic vesion](http://semver.org/)

# how to install
 * __$ composer install__ # to fetches dependencies, executes mandatory deployment commands
  * _NOTE:_ composer is configured to generate __parameters.yml__ using [incenteev/composer-parameter-handler](https://github.com/Incenteev/ParameterHandler)
  * _NOTE:_ composer is configured to create database [if not exists] and apply migrations; __using prod. env.__
 * optional: __$ composer dump-autoload --optimize__ # to generate [class-map autoloader](https://getcomposer.org/doc/03-cli.md#dump-autoload)
  * _NOTE:_ prod. env. uses [APC autoloader](http://symfony.com/doc/current/book/performance.html)
 * optional: __$ php bin/console assets:install__ # to dump assets as hard copies
  * _NOTE:_ by default assets are installed as symlinks

### how to execute tests
 * _$ php bin/phpunit -c ._
 * _$ php bin/behat --strict_
 * _$ php bin/kahlan_
  * _NOTE:_ database\_name\_test in parameters.yml reflects database name for test env.
  * _NOTE:_ test database is wiped and seeded before tests execution

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

            # CORS support.
            RewriteCond %{REQUEST_METHOD} OPTIONS
            RewriteRule ^(.*)$ $1 [R=200,L]
            Header always set Access-Control-Allow-Origin "*"
            Header always set Access-Control-Allow-Methods "POST, GET, PATCH, OPTIONS"

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
