Feature: API: Game Results

  Background:
    Given setup context

  @api
  Scenario Outline:
    Given request API "<routeAlias>" route via "<routeMethod>" with "<routeParam>" "<paramValue>"
    Then observe JSON successful response
    And observe "<expectedAmount>" results in page "<paramValue>"

    Examples:
      | routeAlias                       | routeMethod | routeParam | paramValue | expectedAmount |
      | battleship.game.api.game.results | GET         | page       | 1          | 0              |
      | battleship.game.api.game.results | GET         | page       | 2          | 0              |
      | battleship.game.api.game.results | GET         | page       | 99         | 0              |
