Feature: Slowly playing a few turns
  As a player with time on my hands
  I want to take my time while playing
  So that I can think before acting

  Background:
    Given we're not actually shuffling any decks
    And "player 1" begins in their match against "player 2"

  Scenario: Slowly playing the first turn
    When "player 1" slowly plays a card and attacks
    Then there will be 1 attacker on the battlefield

  Scenario: Slowly playing the first two turns
    Given "player 1" slowly played a card and attacked
    And "player 1" ended their turn
    And "player 2" ended the "defend" phase
    When "player 2" slowly plays a card and attacks
    Then there will be 1 attacker on the battlefield

  Scenario: Slowly playing the first three turns
    Given "player 1" slowly played a card and attacked
    And "player 1" ended their turn
    And "player 2" slowly played a card and attacked
    And "player 2" ended their turn
    When "player 1" slowly plays a card and attacks
    Then there will be 1 attacker on the battlefield
