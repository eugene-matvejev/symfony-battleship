Feature: Battleship Game: GUI: entry page

    @gui
    Scenario: verify index page
        Given I am authorized
        Then request "/" route via "GET"
        And observe response status code "200"
