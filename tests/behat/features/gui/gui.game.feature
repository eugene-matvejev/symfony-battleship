Feature: Game GUI

  @gui
  Scenario: verify index page
    Given request GUI "battleship_game.gui.index" route via "GET" method
    Then observe successful response
