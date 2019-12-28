Feature: Selecting units for the attack
  As a player with units in the match
  I want to order my units to assault the enemy
  In order to crush my enemies, so that I'll win the match

  Background:
    Given we're not actually shuffling any decks
    And "player 1" begins in their match against "player 2"
    And "player 1" already played the "first" card in their hand
    And "player 1" also played what was afterwards the "first" card in their hand
    And "player 1" ended the "play" phase

  Scenario: No attackers before selecting any
    When "player 1" does not select any units for the attack
    Then there will be 0 attackers on the battlefield

  Scenario: Selecting a unit for the attack
    When "player 1" attacks with the "first" unit in their army
    Then there will be 1 attacker on the battlefield

  Scenario: Selecting two units for the attack
    Given "player 1" attacked with the "first" unit in their army
    When "player 1" attacks with the "second" unit in their army
    Then there will be 2 attackers on the battlefield

  Scenario: Not attacking with non-existing cards
    When "player 1" attacks with the "third" unit in their army
    Then that is not possible, because "that card does not exist"

  Scenario: Not attacking in the other player's turn
    When "player 2" attacks with the "first" unit in their army
    Then that is not possible, because "cannot attack at this time"

  Scenario: Ending the turn without attacking
    When "player 1" ends the turn
    Then "player 2" will be in the "play" phase

  Scenario: Ending the turn with attacking
    Given "player 1" attacked with the "first" unit in their army
    When "player 1" ends the turn
    Then "player 2" will be in the "defend" phase

  Scenario: Not attacking after ending the turn
    Given "player 1" ended the turn
    When "player 1" attacks with the "first" unit in their army
    Then that is not possible, because "cannot attack at this time"

  Scenario: Not attacking after the attack phase expired
    Given the "attack" phase expired
    When "player 1" attacks with the "first" unit in their army
    Then that is not possible, because "cannot attack at this time"

  Scenario: Not ending the turn on behalf of another player
    When "player 2" ends the turn
    Then that is not possible, because "cannot end the turn at this time"

  Scenario: Not ending the turn after the turn already expired
    Given the "attack" phase expired
    When "player 1" ends the turn
    Then that is not possible, because "cannot end the turn at this time"
