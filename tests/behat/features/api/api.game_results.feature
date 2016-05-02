Feature: Game Results API

  Background:
    Given setup context

  Scenario Outline: request API
    Given I am requesting "<route>" with "<argument>" and "<value>" API endpoint
    Then I should get successful response
    And there should be <results> results

    Examples:
      | route                            | argument | value | results |
      | battleship.game.api.game.results | page     | 1     | 0       |
      | battleship.game.api.game.results | page     | 2     | 0       |
      | battleship.game.api.game.results | page     | 99    | 0       |

#  Scenario: request API
#    Given all ok
