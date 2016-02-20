Spare time project. Highly experimental.

Project aim: deliver some preview about my technical knowledge before job interview, as well as try 'cutting edge' technologies and approaches.

CPU chip is hardcoded at B2 cell, if you will hit it - it will be instant win, it is done to easier tests manual testing, as the project is far from 'finished' stage, and I keep trying polish it as good as I can.

# Software requirements
 * database: MySQL => 5.5 or MariaDB >= 9.*
 * http server: apache/nginx with php >= 7.0.1
 * composer

### Key Technologies
 * PHP 7.0.1 (because 7.0.0 has bugged primitive types)
 * Symfony Framework 3 (SF3)
 * Doctrine2
 * PHPUnit 5
 * Composer
 * Twig
 * Twitter Bootstrap 3
 * Monolog
 * JavaScript ES6

# How to install
 * copy *app/config/parameters.yml.dist* to *app/config/parameters.yml* and amend database settings
 * *composer install* (will create databases as well as run migrations)
 * *php bin/console assets:install* (as need dump assets once)

### How to launch tests
 * *phpunit -c app* or *php bin/phpunit -c app* (fixtures will wipe and populate database)

# plans for future:
 * deliver back-end as OpenAPI using SF3, PHP7, Doctrine2, Various databases
  * try to create it later as well on Silex.
 * separate front-ent side using single-page-application model AngularJS 2 / Backbone / React
  * front-end already behave as single-page-application (SPA)
 * make simple and flexible database support e.g. MariaDB, MySQL, MongoDB
 * implement phpunit, behat tests, consider kahlan and phpspec as well

### used patterns
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

### github usage:
 * pull-requests to the master

### used standarts:
 * PHP-FIG:
  * ../PSR-2
  * ../PSR-4
