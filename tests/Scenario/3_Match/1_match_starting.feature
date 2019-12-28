Feature: Starting the match
  As a player who just started a new match
  I want to draw my opening hand
  So that I'll have cards to beat my enemies

  Background:
    Given "player 1" has signed up for the game
    And "player 2" has signed up for the game
    And "player 1" proposed a match to "player 2"

  Scenario: No matches until proposals are accepted
    When "player 2" does not accept the proposal
    Then there will be 0 ongoing matches

  Scenario: Accepting the proposal
    Given "player 2" accepted the proposal
    When the match starts
    Then there will be 1 ongoing match

  Scenario Outline: Drawing the opening hands when the match starts
    Given we're not actually shuffling any decks
    Given "player 2" accepted the proposal
    When the match starts
    Then "<each>" will have the following cards in their hand:
      | Card        | Mana cost |
      | card-type-1 | 1         |
      | card-type-2 | 3         |
      | card-type-3 | 4         |
      | card-type-4 | 6         |
      | card-type-5 | 2         |
      | card-type-6 | 5         |
      | card-type-7 | 2         |
    Examples:
      | each     |
      | player 1 |
      | player 2 |

  Scenario: One of the players has the first turn
    Given "player 2" accepted the proposal
    When the match starts
    Then either "player 1" or "player 2" will get the first turn
