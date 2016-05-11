Feature: API: Game

  @api
  Scenario Outline: request API
    Given request API "<routeAlias>" route via "<routeMethod>" with "<routeParam>" "<paramValue>"
    Then observe unsuccessful response

    Examples:
      | routeAlias               | routeMethod | routeParam | paramValue |
      | battleship.game.api.init | POST        |            |            |
      | battleship.game.api.turn | PATCH       | cellId     | 1          |
