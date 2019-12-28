Feature: Blocking the enemy attackers
  As a player under attack by enemy units
  I want to send my units to counter-attack
  So that I can block the attackers and survive the day

  Background:
    Given we're not actually shuffling any decks
    And "player 1" begins in their match against "player 2"

    Given "player 1" played the "first" card in their hand
    And "player 1" played what was then the "first" card in their hand
    And "player 1" ended the "playing" phase
    And "player 1" ended the turn

    Given "player 2" played the "first" card in their hand
    And "player 2" played what was then the "first" card in their hand
    And "player 2" ended the "playing" phase
    And "player 2" attacked with the "first" unit in their army
    And "player 2" attacked with the "second" unit in their army
    And "player 2" ended the turn

  Scenario: No defenders before selecting any
    When "player 1" does not select any units for defending
    Then there will be 0 defenders on the battlefield
    But there will be 4 units on the battlefield

  Scenario: Blocking an enemy
    When "player 1" uses their "first" unit to block the "first" attacker
    Then there will be 1 defender on the battlefield

  Scenario: Blocking two enemies
    Given "player 1" used their "first" unit to block the "first" attacker
    When "player 1" uses their "second" unit to block the "second" attacker
    Then there will be 2 defenders on the battlefield

  Scenario: Killing an attacker
    Given "player 1" uses their "second" unit to block the "first" attacker
    When "player 1" ends the "defending" phase
    Then there will be 3 units on the battlefield
    And "player 1" will have 2 units
    And "player 2" will have 1 unit

  Scenario: Dying while defending
    Given "player 1" uses their "first" unit to block the "second" attacker
    When "player 1" ends the "defending" phase
    Then there will be 3 units on the battlefield
    And "player 1" will have 1 unit
    And "player 2" will have 2 units

  Scenario: Not blocking after the defend phase expired
    Given the "defend" phase expired
    When "player 1" uses their "first" unit to block the "first" attacker
    Then that is not possible, because "cannot block at this time"

  Scenario: Not blocking in the enemy turn
    When "player 2" uses their "first" unit to block the "first" attacker
    Then that is not possible, because "cannot block at this time"

  Scenario: Not blocking with non-existing units
    When "player 1" uses their "fifth" unit to block the "first" attacker
    Then that is not possible, because "no such defender"

  Scenario: Not blocking if there are no attackers
    Given "player 1" ended the turn
    And "player 2" ended the turn
    When "player 1" uses their "first" unit to block the "first" attacker
    Then that is not possible, because "cannot block at this time"

  Scenario: Not ending the blocking phase of the enemy turn
    When "player 2" ends the "defending" phase
    Then that is not possible, because "cannot start the combat at this time"

  Scenario: Automatically performing combat when the defence phase expires
    Given "player 1" uses their "second" unit to block the "first" attacker
    When the "defend" phase expires
    Then there will be 3 units on the battlefield
    And "player 1" will have 2 units
    And "player 2" will have 1 unit
