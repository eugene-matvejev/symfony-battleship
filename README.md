# Battleship Game API
### Spare time project, Work in Progress! Highly experimental!!!
#### Project aim
 * deliver some preview about my technical knowledge before job interview
  * try 'cutting edge' technologies and approaches.

### cheat-code:
 * CPU have only one-cell ship which is hardcoded at __B2__ cell
  * if you will hit it __B2__ cell - it will be instant win
   * it is done to easier tests manual testing, as the project is far from 'finished' stage, as I keep trying polish it.

### plans for future:
 * deliver back-end as OpenAPI using SF3, PHP7, Doctrine2, Various databases
  * try to create it later as well on Silex.
 * separate front-ent side using single-page-application model AngularJS 2 / Backbone / React
  * front-end already behave as single-page-application (SPA)
 * make simple and flexible database support e.g. MariaDB, MySQL, MongoDB
 * implement phpunit, behat tests, consider kahlan and phpspec as well

# Software requirements
 * database: MySQL => 5.5 or MariaDB >= 9.*
  * MongoDB support WIP 
 * http server: apache/nginx with php >= 7.0.1
 * composer

### Key Technologies
 * PHP 7.0.1 (because 7.0.0 had bugged primitive types)
 * Symfony Framework 3 (SF3)
 * Doctrine 2
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
