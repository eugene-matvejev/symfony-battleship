Feature: Battleship Game: API: Game Mechanics

  @api
  Scenario Outline: routes should return unsuccessful response on wrong data
    Given request "<routeAlias>" API route via "<routeMethod>" with "<routeParam>" "<paramValue>"
    Then observe unsuccessful response
    And observe response status code "<expectedStatusCode>"

    Examples:
      | routeAlias               | routeMethod | routeParam | paramValue | expectedStatusCode |
      | battleship_game.api.init | POST        | ~          | ~          | 400                |
      | battleship_game.api.turn | PATCH       | cellId     | 0          | 404                |
