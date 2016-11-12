Feature: Battleship Game: API: Game Results

  @api
  Scenario Outline: routes should return successful response
    Given request API "<route>" route via "<method>"
    Then observe response status code "<code>"
    And observe valid JSON response

    Examples:
      | method | route                     | code |
      | GET    | /api/game-results/page/1  | 200  |
      | GET    | /api/game-results/page/2  | 200  |
      | GET    | /api/game-results/page/99 | 200  |
