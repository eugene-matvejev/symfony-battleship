My spare time project.
Higly expremental.

Used:
 * PHP 7.0.1 (because 7.0.0 has bugged primitive types)
 * Symfony Framework 3 (Symfony3, SF3)
 * Doctrine2
 * Twig
 * Twitter Bootstrap 3
 * PHPUnit 5

How to install. (to setup you need use composer, execute all commands from project root directory)
 * composer install (will create databases as well)
  (it will setup database and run migrations automatically)
 * php bin/console assets:install

for launch unit tests you need PHPUnit
 * phpunit -c app (fixtures will wipe and populate database)

----------
more details:

used patterns:
 * FrontController
 * MVC
 * ORM
 * Builder
 * Strategy
 * Factory
 * Singleton
 * Service Locator
 * Registry
 * EventDispatcher
 * Data Mapper
 * Dependency Injection

used frameworks/bundles:
 * Symfony3
 * ../console
 * ../yams
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
