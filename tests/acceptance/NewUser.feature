Feature: NewUser
  As a curator
  I want to submit a form with user details
  So that I can add a new user to GigaDB database

  Scenario: user creation form
    Given I am on "/site/login"
    When I follow "Create account"
    Then I should see "Registration"

