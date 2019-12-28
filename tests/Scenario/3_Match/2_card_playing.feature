Feature: Playing cards
  As a player who just got the first turn in a new match
  I want to play cards onto the battlefield
  So that I'll get an edge over my opponent

  Background:
    Given we're not actually shuffling any decks
    And "player 1" begins in their match against "player 2"

  Scenario: No cards in play before any were played
    When nobody plays any cards
    Then the battlefield will be empty
    And "player 1" will be in the "play" phase

  Scenario: Playing the first card
    When "player 1" plays the "first" card in their hand
    Then there will be 1 unit on the battlefield
    And "player 1" will have 6 cards left in their hand

  Scenario: Playing two cards
    Given "player 1" already played the "first" card in their hand
    When "player 1" plays the then "first" card in their hand
    Then there will be 2 units on the battlefield
    And "player 1" will have 5 cards left in their hand

  Scenario: Mana runs out
    Given mana can run out
    And "player 1" already played the "first" card in their hand
    And "player 1" also played what was afterwards the "first" card in their hand
    When "player 1" plays the then "first" card in their hand
    Then that is not possible, because "not enough mana"
    But there will still be 2 units on the battlefield
    And "player 1" will have 5 cards left in their hand

  Scenario: Playing a spell
    Given the "third" card in the deck is a spell
    When "player 1" plays the "third" card in their hand
    Then the battlefield will be empty
    And "player 1" will have 6 cards left in their hand

  Scenario: Not playing in the enemy turn
    When "player 2" plays the "first" card in their hand
    Then that is not possible, because "cannot play cards right now"
    And the battlefield will be empty

  Scenario: Ending the card playing phase
    When "player 1" ends the "play" phase
    Then "player 1" will be in the "attack" phase

  Scenario: Not ending the card playing phase for another player
    When "player 2" ends the "play" phase
    Then that is not possible, because "cannot end the card playing phase"
    And "player 1" will still be in the "play" phase

  Scenario: Not playing cards after ending the phase
    Given "player 1" ended the "play" phase
    When "player 1" plays the "first" card in their hand
    Then that is not possible, because "cannot play cards"
    And the battlefield will be empty

  Scenario: Not playing cards after the play phase expired
    Given the "play" phase expired
    When "player 1" plays the "first" card in their hand
    Then that is not possible, because "cannot play cards"
    And the battlefield will be empty

  Scenario: Playing a card while another match is also going on
    Given "player 1" begins in their match "A" against "player 2"
    And "player 3" begins in their match "B" against "player 4"
    When "player 1" plays the "first" card in their hand
    Then there will be 1 unit on the battlefield of match "A"
    But the battlefield of match "B" will still be empty

  Scenario: Not attacking during the play phase
    Given "player 1" already played the "first" card in their hand
    When "player 1" attacks with the "first" unit in their army
    Then that is not possible, because "cannot attack"
