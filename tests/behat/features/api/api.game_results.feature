Feature: Battleship Game: API: Game Results

  @api
  Scenario Outline:
    Given request API "<route_alias>" route via "<route_method>" with "<route_param>" "<param_value>"
    Then observe JSON successful response
    And observe "<expected_amount>" results in page "<param_value>"

    Examples:
      | route_alias                      | route_method | route_param | param_value | expected_amount |
      | battleship_game.api.game.results | GET          | page        | 1           | 0               |
      | battleship_game.api.game.results | GET          | page        | 2           | 0               |
      | battleship_game.api.game.results | GET          | page        | 99          | 0               |
