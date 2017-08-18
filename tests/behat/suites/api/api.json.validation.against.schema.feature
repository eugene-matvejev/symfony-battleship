Feature: Battleship Game: API: Game Results

  @api
  @jsonschema
  Scenario Outline: responses should match schemas
    Given submit data from "<json>" to API "<route>" via "<method>"
    Then validate response against schema "<schema>"

    Examples:
      | method | route | json                                    | schema                               |
      | POST   | /api/ | game.initiation.request.1.opponent.json | game.initiation.response.schema.json |
