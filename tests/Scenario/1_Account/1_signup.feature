Feature: Signing up for the game
  As a visitor, eager to play
  I want to create an account
  So that I can propose matches to other players

  Scenario: Opening a guest account
    Given I visited the "home" page
    When I open an account
    Then my account will be a guest account

  Scenario: Not opening an account without visiting a page first
    When I open an account
    Then that is not possible, because "cannot open account for unknown entity"
    And I will not have an account

  Scenario: Before anyone signed up, the player list is empty
    When I visit the "player list" page
    Then the player list will be empty

  Scenario: After signing up, the player list is not empty
    Given I visited the "home" page
    When I open an account
    Then the player list will not be empty
