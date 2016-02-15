My spare time project.
Highly experimental.

# Plans:
* deliver back-end as OpenAPI using SF3, PHP7, Doctrine2, Various databases
* try to create it later as well on Silex.
* separate front-ent side using single-page-application model AngularJS 2 / Backbone / React
* front-end already behave as single-page-application (SPA)

You need MySQL => 5.5 / MariaDB >= 9.*
apache/nginx
php >= 7.0.1

# Key Technologies
 * PHP 7.0.1 (because 7.0.0 has bugged primitive types)
 * Symfony Framework 3 (SF3)
 * Doctrine2
 * PHPUnit 5
 * Composer
 * Twig
 * Twitter Bootstrap 3
 * JavaScript ES6

# How to install
copy app/config/parameters.yml.dist to app/config/parameters.yml and database settings

to setup you need use composer and execute those commands from project root directory:
 * composer install (will create databases as well run migrations)
 * php bin/console assets:install (to run app in production mode)

# How to launch tests
 * phpunit -c app (fixtures will wipe and populate database)

# more details
---
used patterns:
 * Front Controller
 * MVC
 * ORM
 * Data Mapper
 * Builder
 * Strategy
 * Factory
 * Singleton
 * Service Locator
 * Registry
 * Event Dispatcher
 * Dependency Injection

used frameworks/bundles:
 * Symfony3
 * ../console
 * ../yaml
 * Doctrine2
 * ../fixtures
 * ../migrations
 * Twig
 * Monolog
 * Composer
 * PHPUnit

github usage:
 * pull-requests to the master

used standarts:
 * PHP-FIG:
 * ../PSR-2
 * ../PSR-4
 * ../PSR-7
