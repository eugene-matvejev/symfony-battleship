Feature: Battleship Game: GUI: entry page

  @gui
  Scenario: verify index page
    Given request GUI "battleship_game.gui.index" route via "GET" method
    Then observe redirected response
