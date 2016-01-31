My spare time project.
Higly expremental.

Used:
 * PHP 7.0.1
 * Symfony Framework 3
 * Doctrine2
 * Twig
 * Twitter Bootstrap 3
 * PHPUnit 5

How to install. (to setup you need use composer, execute all commands from project root directory)
 * composer install (will create databases as well)
 * php bin/console assets:install

php bin/console doctrine:migrations:migrate --env=prod

for launch unit tests you need PHPUnit
 * phpunit -c app (fixtures will wipe and populate database)


patterns:
 * FrontController
 * MVC
 * ORM
 * Strategy
 * Factory
 * Singleton
 * Services
 * Container
 * EventDispatcher
 * DependencyInjection

frameworks/bundles:
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

github:
 * pull-requests


PHP:
 * 7.0.1
 * PHP-FIG:
 * ../PSR-2
 * ../PSR-4
 * ../PSR-7
