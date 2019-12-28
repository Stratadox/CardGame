Feature: Match proposals
  As a player with an account
  I want to propose and accept matches
  In order to play a match

  Background:
    Given "player 1" has signed up for the game
    And "player 2" has signed up for the game

  Scenario: No proposals until a match is proposed
    When no proposals are made
    Then "player 1" will have 0 open match proposals
    And "player 2" will have 0 open match proposals

  Scenario: Proposing a match
    When "player 1" proposes a match to "player 2"
    Then "player 2" will have 1 open match proposal

  Scenario: No accepted proposals until a match is accepted
    When "player 1" proposes a match to "player 2"
    Then there will not be any accepted proposals yet

  Scenario: Accepting a proposal
    Given "player 1" proposed a match to "player 2"
    When "player 2" accepts the proposal
    Then there will be 1 accepted proposal

  Scenario: Not accepting non existing proposals
    When "player 2" accepts the proposal
    Then that is not possible, because "proposal not found"
    And there will not be any accepted proposals

  Scenario: Expired proposals are not open proposals anymore
    Given "player 1" proposed a match to "player 2"
    When the proposal expires
    Then "player 2" will have 0 open match proposals

  Scenario: Not accepting expired proposals
    Given "player 1" proposed a match to "player 2"
    But the proposal has expired
    When "player 2" accepts the proposal
    Then that is not possible, because "the proposal has already expired!"
    And there will not be any accepted proposals

  Scenario: Accepting a proposal just in time
    Given "player 1" proposed a match to "player 2"
    And the proposal has almost expired
    When "player 2" accepts the proposal
    Then there will be 1 accepted proposal

  Scenario: Not accepting proposals on behalf of others
    Given "player 1" proposed a match to "player 2"
    When "player 1" accepts the proposal
    Then that is not possible, because "proposal not found"
    And there will not be any accepted proposals yet
