Feature: Game Results API

    Background:
       Given setup context

    Scenario: successfully fetch game results
        Given request api endpoint
         Then get results
          And there are 10 results

