Feature: Battleship Game: GUI: entry page

  @gui
  Scenario: verify index page
    Given request "foundation_bundle.gui.index" route via "GET"
    Then observe redirected response
