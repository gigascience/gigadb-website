Feature: NewUser
  As a curator
  I want a form to enter user details
  So that I can add a new user to GigaDB database

  Scenario: form for creating new user
    Given I am on "/site/login"
    When I follow "Create account"
    Then I should see "Registration"
    And I should not see "Link this user to an author"

