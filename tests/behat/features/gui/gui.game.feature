Feature: index page

  Background:
#    Given I am viewing the index page

  Scenario: Successful game initiation
    Given I am viewing the index page
    When loading has been finished
    Then new game been initiated
    And human player have at least one live ship
    And cpu player have at least one live ship

