Feature: form to update dataset details
  As a curator
  I want a form to update user's details
  So that the user information is up-to-date

  Background:
    Given I have signed in as admin

  @ok
  Scenario: Update user successfully
    Given I am on "/user/update/id/22"
    And I should not see "Password"
    And I should not see "Confirm Password*"
    When I fill in the field of "id" "User_affiliation" with "BGI test"
    And I check "User_terms" checkbox
    Then I press the button "Save"
    And I should not see "Captcha is required"
    And I should be on "/user/view/id/22"
    And I should see "BGI test"

  @ok
  Scenario: Fail to update user because of duplicate email
    Given I am on "/user/update/id/22"
    And I should not see "Password"
    And I should not see "Confirm Password*"
    When I fill in the field of "id" "User_email" with "test+163@gigasciencejournal.com"
    And I check "User_terms" checkbox
    Then I press the button "Save"
    And I should see "Fail to update!"
    And I should see an error message "Email \"test+163@gigasciencejournal.com\" has already been taken."