Feature: Battleship Game: API: Game Mechanics

  @api
  Scenario Outline: routes should return unsuccessful response on wrong data
    Given request API "<route_alias>" route via "<route_method>" with "<route_param>" "<param_value>"
    Then observe unsuccessful response
    And observe response status code "<expected_status_code>"

    Examples:
      | route_alias              | route_method | route_param | param_value | expected_status_code |
      | battleship_game.api.init | POST         | ~           | ~           | 500                  |
      | battleship_game.api.turn | PATCH        | cellId      | 1           | 500                  |
