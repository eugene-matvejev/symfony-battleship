Feature: API: Game

  @api
  Scenario Outline: request API
    Given request API "<routeAlias>" route via "<routeMethod>" with "<routeParam>" "<paramValue>"
    Then observe unsuccessful response

    Examples:
      | routeAlias               | routeMethod | routeParam | paramValue |
      | battleship_game.api.init | POST        |            |            |
      | battleship_game.api.turn | PATCH       | cellId     | 1          |
