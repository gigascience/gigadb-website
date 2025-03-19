@ok-needs-secrets
Feature: EditUser
  As a curator
  I want a form to edit user details
  So that I can edit a user to GigaDB database

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Ensure I can edit a user without changing the password
    Given I am on "/user/update/id/8"
    And I fill in the field of "id" "User_first_name" with "modified"
    And I check the field "User_terms"
    And I press the button "Save"
    Then I should see "View User #8"
    And I should see "modified"

  @ok
  Scenario: Ensure I can't edit a user without matching password
    Given I am on "/user/update/id/8"
    And I fill in the field of "id" "User_password" with "Azertyu1@"
    And I fill in the field of "id" "User_password_repeat" with "Azertyu2@"
    And I check the field "User_terms"
    And I press the button "Save"
    Then I should not see "View User #8"

  @ok
  Scenario: Ensure I can't edit a user without matching password regex
    Given I am on "/user/update/id/8"
    And I fill in the field of "id" "User_password" with "123"
    And I fill in the field of "id" "User_password_repeat" with "123"
    And I check the field "User_terms"
    And I press the button "Save"
    Then I should not see "View User #8"

  @ok
  Scenario: Ensure I can edit a user with matching password
    Given I am on "/user/update/id/8"
    And I fill in the field of "id" "User_password" with "Azertyu1@"
    And I fill in the field of "id" "User_password_repeat" with "Azertyu1@"
    And I check the field "User_terms"
    And I press the button "Save"
    Then I should see "View User #8"
