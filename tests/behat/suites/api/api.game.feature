Feature: Battleship Game: API: Game Mechanics

    @api
    @mechanics
    Scenario Outline: routes should return unsuccessful response on wrong data
        Given I am authorized
        When request API "<route>" route via "<method>"
        Then observe response status code "<code>"

        Examples:
            | method | route                    | code |
            | POST   | /api/game-init           | 400  |
            | PATCH  | /api/game-turn/cell-id/0 | 404  |
