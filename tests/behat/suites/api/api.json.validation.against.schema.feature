Feature: Battleship Game: API: schema validation

  @api
  @jsonschema
  Scenario Outline: API responses should match schemas
    Given submit data from "<json>" to API "<route>" via "<method>"
    Then validate response against schema "<schema>"

    Examples:
      | method | route                    | json                              | schema                            |
      | POST   | /api/game-init           | game.init.request.1.opponent.json | game.init.response.schema.json    |
      | GET    | /api/game-results/page/1 | game.results.request.schema.json  | game.results.response.schema.json |
