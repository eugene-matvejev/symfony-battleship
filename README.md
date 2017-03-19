[circle.ci-master-badge]: https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master.svg?style=svg
[circle.ci-master-link]: https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/master
[codecov.io-master-badge]: https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/master/graph/badge.svg
[codecov.io-master-link]: https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/master

[circle.ci-heroku-badge]: https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/heroku.svg?style=svg
[circle.ci-heroku-link]: https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/heroku
[codecov.io-heroku-badge]: https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/heroku/graph/badge.svg
[codecov.io-heroku-link]: https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/heroku

[circle.ci-prototype-badge]: https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/prototype_authorization.svg?style=svg
[circle.ci-prototype-link]: https://circleci.com/gh/eugene-matvejev/battleship-game-api/tree/prototype_authorization
[codecov.io-prototype-badge]: https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/prototype_authorization/graph/badge.svg
[codecov.io-prototype-link]: https://codecov.io/gh/eugene-matvejev/battleship-game-api/branch/prototype_authorization

|                         | master                                                         | heroku                                                                | < authorization prototype >                                                    
|---                      |---                                                             |---                                                                    |---
| __TDD tests__           |
| _< Circle CI >_ PHPUnit | [![build][circle.ci-master-badge]][circle.ci-master-link]      | [![build][circle.ci-heroku-badge]][circle.ci-heroku-link]             | [![build][circle.ci-prototype-badge]][circle.ci-prototype-link]
| __BDD tests__           |
| _< Circle CI >_ Behat   | [![build][circle.ci-master-badge]][circle.ci-master-link]      | [![build][circle.ci-heroku-badge]][circle.ci-heroku-link]             | [![build][circle.ci-prototype-badge]][circle.ci-prototype-link]
| _< Circle CI >_ Kahlan  | [![build][circle.ci-master-badge]][circle.ci-master-link]      | [![build][circle.ci-heroku-badge]][circle.ci-heroku-link]             | [![build][circle.ci-prototype-badge]][circle.ci-prototype-link]
| __coverage__            |
| _< codecov.io >_        | [![coverage][codecov.io-master-badge]][codecov.io-master-link] | [![coverage][codecov.io-heroku-badge]][codecov.io-heroku-link]        | [![coverage][codecov.io-prototype-badge]][codecov.io-prototype-link]

other CI engines reports _[on master]_

[![scrutinizer-code-quality](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/?branch=master)
[![scrutinizer-code-coverage](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/eugene-matvejev/battleship-game-api/?branch=master)
[![code-climate-code-quality](https://codeclimate.com/github/eugene-matvejev/battleship-game-api/badges/gpa.svg)](https://codeclimate.com/github/eugene-matvejev/battleship-game-api)
[![travis-build-status-master](https://travis-ci.org/eugene-matvejev/battleship-game-api.svg?branch=master)](https://travis-ci.org/eugene-matvejev/battleship-game-api)
[![sensio-insight-framework](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc/mini.png)](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc)
[![codeship-build-status-master](https://codeship.com/projects/e893a4f0-0b28-0134-b0ad-129a07c0a376/status?branch=master)](https://codeship.com/projects/155781)


__DEMO__ : https://battleship-game-api.herokuapp.com/ _[API]_

# Battleship Game API
##### THIS IS SPARE TIME PROJECT, WORK IN PROGRESS! HIGHLY EXPERIMENTAL!!!
#### project purpose
 * try out:
  * _cutting edge_ technologies such as PHP7, SF3, Doctrine2
  * _modern_ approaches such as Test Automation, Continuous Integration|Deployment
 * simulate database loading [~500 transactions per request]
 * deliver preview about my technical knowledge before the job interview

#### game cheat-code
* AI players have only one ship[single-cell] which is located at __B2__ cell [_purpose: easier manual testing_]
 * if you will hit __B2__ cell - you will win

## software requirements
 * supported database engines
  * MySQL >= 5.5
  * MariaDB >= 9.0
  * PostgreSQL >= 9.3
  * SQLite >= 3
 * http server: apache/nginx with PHP7
 * Composer >= 1.0.3

## technology stack
### key technologies
 * PHP7 (7.0.0 - 7.0.4 || >= 7.0.6 [7.0.5 had bugged SPL])
 * [Symfony Framework 3](http://symfony.com) [SF3]
 * [Doctrine 2](http://doctrine-orm.readthedocs.io/en/latest) with [Fixtures](http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html)
 * [Composer](https://getcomposer.org)
 * [JMS Serializer](http://jmsyst.com/bundles/JMSSerializerBundle)
 * [API Doc](https://packagist.org/packages/nelmio/api-doc-bundle)
 * [Twig](http://twig.sensiolabs.org)
 * [PHPUnit 5](https://phpunit.de)
 * [Behat 3](http://docs.behat.org/en/v3.0)
 * [Kahlan](http://kahlan.readthedocs.io/en/latest)

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

### PHP-FIG
 * [PSR-1](http://www.php-fig.org/psr/psr-1/)
 * [PSR-2](http://www.php-fig.org/psr/psr-2/)
 * [PSR-3](http://www.php-fig.org/psr/psr-3/)
 * [PSR-4](http://www.php-fig.org/psr/psr-4/)
 * [PSR-6](http://www.php-fig.org/psr/psr-6/)
 * [PSR-11](http://www.php-fig.org/psr/psr-11/)

## workflow
 * new functionality merged into master branch only via pull requests
 * each pull request have callback to trigger CI engines such as Travis, Circle, CodeShip, Scrutinizer, Sensiolabs Insight, CodeCov
 * result of each pull request is ready-to-use release - using Continuous Delivery principles

#gitflow
  * __master__ branch: stable source code, contains release-ready source
  * __heroku__ branch: reflects current deployed app at heroku [Continuous Deployment]
  * __prototype_*__ branch: contains new idea [pull request of prototype branch is always next _major_ version release]
  * pull requests follows [semantic vesion](http://semver.org)

## how to install
 * `$ composer install` to fetches dependencies, executes mandatory deployment commands
  * _NOTE:_ composer is configured to generate __parameters.yml__ using [incenteev/composer-parameter-handler](https://github.com/Incenteev/ParameterHandler)
  * _NOTE:_ composer is configured to create database [if not exists] and apply migrations; __using prod. env.__
 * optional: `$ composer dump-autoload --optimize` to generate [class-map autoloader](https://getcomposer.org/doc/03-cli.md#dump-autoload)
  * _NOTE:_ prod. env. uses [APC autoloader](http://symfony.com/doc/current/book/performance.html)
 * optional: `$ php bin/console assets:install` to dump assets as hard copies
  * _NOTE:_ by default assets are installed as symlinks

### how to execute tests
 * `$ php bin/console doctrine:database:create --env=test`
 * `$ php bin/console doctrine:migrations:migrate --env=test`
 * `$ php bin/console doctrine:fixtures:load --env=test`
 * `$ php bin/phpunit -c .`
 * `$ php bin/behat`
 * `$ php bin/kahlan`
  * _NOTE:_ database\_name\_test in parameters.yml reflects database name for test env.
  * _NOTE:_ test database is wiped and seeded before tests execution
 * OPTIONAL:
  * `$ ant test` launch all tests in order [phpunit, behat, kahlan]

### config examples
 * [apache](https://github.com/eugene-matvejev/battleship-game-api/blob/docs/apache.config.example.md)
