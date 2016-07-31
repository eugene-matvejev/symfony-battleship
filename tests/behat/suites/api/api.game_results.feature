Feature: Battleship Game: API: Game Results

  @api
  Scenario Outline: routes should return successful response
    Given request "<routeAlias>" API route via "<routeMethod>" with "<routeParam>" "<paramValue>"
    Then observe successful JSON response
    And observe "<expectedAmount>" results in page "<paramValue>"

    Examples:
      | routeAlias                       | routeMethod | routeParam | paramValue | expectedAmount |
      | battleship_game.api.game.results | GET         | page       | 1          | 0              |
      | battleship_game.api.game.results | GET         | page       | 2          | 0              |
      | battleship_game.api.game.results | GET         | page       | 99         | 0              |
