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

[![code-climate-code-quality](https://codeclimate.com/github/eugene-matvejev/battleship-game-api/badges/gpa.svg)](https://codeclimate.com/github/eugene-matvejev/battleship-game-api)
[![travis-build-status-master](https://travis-ci.org/eugene-matvejev/battleship-game-api.svg?branch=master)](https://travis-ci.org/eugene-matvejev/battleship-game-api)
[![sensio-insight-framework](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc/mini.png)](https://insight.sensiolabs.com/projects/f92d83b6-fd11-4b1b-ae86-b3ba1fb152dc)
[![codeship-build-status-master](https://codeship.com/projects/e893a4f0-0b28-0134-b0ad-129a07c0a376/status?branch=master)](https://codeship.com/projects/155781)


__DEMO__ : https://battleship-game-api.herokuapp.com/ _[out of sync with master, because front-end is not ready yet]_

# Battleship Game API
##### THIS IS SPARE TIME PROJECT, WORK IN PROGRESS! HIGHLY EXPERIMENTAL!!!
#### project purpose
 * to try out _cutting edge_ technologies and services,  _modern_ approaches such as Test Automation, Continuous Integration{CI}|Deployment{CD}
 * simulate database loading [~500 transactions per request]
 * deliver preview about my technical knowledge before the job interview
 * demonstrate technical knowledege level prior job interview

#### game cheat-code
* AI players have only one ship[single-cell] which is located at __B2__ cell [_purpose: easier manual testing_]
  * if you will hit __B2__ cell - you will win

## software requirements
 * PHP 7.1
 * one of supported database engines
   * MySQL >= 5.5
   * MariaDB >= 9.0
   * PostgreSQL >= 9.3
   * SQLite >= 3
 * http server with CGI e.g. [example: apache, nginx]
 * composer >= 1.0.3

## technology stack
### key technologies
 * PHP 7.1
 * [Symfony Framework 3](http://symfony.com) [SF3]
 * [Doctrine 2](http://doctrine-orm.readthedocs.io/en/latest) with [Fixtures](http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html)
 * [Composer](https://getcomposer.org)
 * [JMS Serializer](http://jmsyst.com/bundles/JMSSerializerBundle)
 * [API Doc](https://packagist.org/packages/nelmio/api-doc-bundle)
 * [Twig](http://twig.sensiolabs.org)
 * [PHPUnit 5](https://phpunit.de)
 * [Behat 3](http://docs.behat.org/en/v3.0)
 * [Kahlan](http://kahlan.readthedocs.io/en/latest)
 * [json schema](http://json-schema.org/)

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
 * each pull request have callback to trigger CI engines such as Travis, Circle, CodeShip, Sensiolabs Insight, CodeCov
 * result of each pull request is ready-to-use release - using Continuous Delivery principles

## gitflow
  * __master__ branch: stable source code, contains release-ready source
  * __heroku__ branch: reflects current deployed app at heroku [Continuous Deployment]
  * __prototype_*__ branch: contains new idea [pull request of prototype branch is always next _major_ version release]
  * pull requests follows [semantic vesion](http://semver.org)

## how to install
 * `$ composer install` to fetch dependencies and execute mandatory deployment steps
   * _NOTE:_ composer is configured to generate __parameters.yml__ using [incenteev/composer-parameter-handler](https://github.com/Incenteev/ParameterHandler)
 * optional `$ composer dump-autoload --classmap-authoritative` to generate [class-map autoloader](https://getcomposer.org/doc/03-cli.md#dump-autoload)

### how to execute tests
  if you've __ant__ installed locally, you can run all tests via shortcut command: `$ ant test` or just `$ ant`
  database\_name\_test in parameters.yml reflects database name for the test env.
  test database need to be wiped and seeded prior tests execution (for PHPUnit tests only, Kahlan and Behat tests can be executed on 'dirty' database)

sequence of steps to prepare test database
 * `$ php bin/console doctrine:database:create --env=test`
 * `$ php bin/console doctrine:migrations:migrate --env=test`
 * `$ php bin/console doctrine:fixtures:load --env=test`

how to execute tests
 * `$ php bin/phpunit -c .` or `$ php bin/phpunit -c . --no-coverage` _to disable coverage report_
 * `$ php bin/behat`
 * `$ php bin/kahlan`

### http server config examples
 * [apache](https://github.com/eugene-matvejev/battleship-game-api/blob/master/docs/apache.config.example.md)
