Feature: Battleship Game: GUI: entry page

  @gui
  Scenario: verify index page
    Given request "/" route via "GET"
    And observe response status code "200"
#    And observe redirection to "/api/documentation"
