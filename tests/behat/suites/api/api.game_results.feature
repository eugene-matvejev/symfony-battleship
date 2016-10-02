Feature: Battleship Game: API: Game Results

  @api
  Scenario Outline: routes should return successful response
    Given request API "<route>" route via "<method>"
    Then observe response status code "<statusCode>"
    And observe valid JSON response
    And observe "<amount>" results in response

    Examples:
      | method | route                     | statusCode | amount |
      | GET    | /api/game-results/page/1  | 200        | 0      |
      | GET    | /api/game-results/page/2  | 200        | 0      |
      | GET    | /api/game-results/page/99 | 200        | 0      |
