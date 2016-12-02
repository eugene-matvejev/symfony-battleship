Feature: Battleship Game: API: Game Mechanics

    @api
    @mechanics
    Scenario Outline: routes should return unsuccessful response on wrong data
        Given I am authorized
        When request API "<route>" route via "<method>"
        Then observe response status code "<statusCode>"

        Examples:
            | method | route                | statusCode |
            | POST   | /api/player/register | 400        |
            | POST   | /api/player/login    | 400        |
            | DELETE | /api/player/logout   | 403        |