Feature: Game Results API

  Background:
    Given setup context

  Scenario Outline: request API
    Given requesting "<route>" with "<arg>" and "<argValue>" API endpoint
    Then observe successful response
    And there should be "<argValue>" and have "<expectedAmountOfGameResults>" results

    Examples:
      | route                            | arg  | argValue | expectedAmountOfGameResults |
      | battleship.game.api.game.results | page | 1        | 0                           |
      | battleship.game.api.game.results | page | 2        | 0                           |
      | battleship.game.api.game.results | page | 99       | 0                           |
